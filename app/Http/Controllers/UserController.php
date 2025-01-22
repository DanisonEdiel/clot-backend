<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageExceptions;
use App\Models\User;
use App\Models\TenantUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\InviteUserToTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users =  User::join('tenant_user', function ($join) {
            $join->on('users.id', '=', 'tenant_user.user_id');
        })
            ->where('tenant_user.tenant_id', $request->header("X-Tenant"))->whereNot('tenant_user.role_id', 1)->get();
        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $user = User::firstWhere('email', $request->email);
            $temporalPassword = null;
            if (!$user) {
                $temporalPassword = Str::random(15);
                DB::beginTransaction();
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password =  Hash::make($temporalPassword);
                $user->save();
            }

            $tenantUser = TenantUser::where('tenant_id', $request->header("X-Tenant-Id"))->where('user_id', $user->id)->first();

            if ($tenantUser) {
                throw new MessageExceptions([
                    'El usuario con el email ' . $user->email . ' ya se encuentra en la empresa!',
                ]);
            }

            $tenantUser = new TenantUser();
            $tenantUser->user_id = $user->id;
            $tenantUser->role_id = 2;
            $tenantUser->tenant_id = $request->header("X-Tenant");
            $tenantUser->save();

            $correo = new InviteUserToTenant($request->email, $temporalPassword);

            Mail::to($request->email)->send($correo);

            DB::commit();
            return response()->json(['success' => $user], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return User::find($id);
        } catch (\Throwable $th) {
            return response()->json(['message' => '¡Usuario no encontrado!'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => '¡Usuario no encontrado!'], 401);
        }

        try {
            $user->update($request->all());
            return response()->json(['success' => $user], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $user = DB::table('tenant_user')->where('user_id', $id)->where('tenant_id', $request->header('X-Tenant'));

        if (!$user) {
            throw new MessageExceptions([
                '¡Usuario no encontrado!',
            ]);
        }

        try {
            $user->delete();
            return response()->json(['message' => '¡Usuario eliminado!'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }
}
