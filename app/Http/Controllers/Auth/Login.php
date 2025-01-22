<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\MessageExceptions;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', strtolower($request->email))->with('tenantUser.role')->first();
        if (!$user) {
            throw new MessageExceptions([
                'Credenciales invalidas!',
            ]);

        }
        $tenantIds = DB::table('tenant_user')->where('user_id', $user->id)->pluck('tenant_id');
        $tenants = Tenant::whereIn('id', $tenantIds)->with('owner')->get();
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->delete();
        if (
            $user &&
            Hash::check($request->password, $user->password)
        ) {
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            if ($tenants->count() == 1) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_id', $user->id)
                    ->update(['tenant_id' => $tenants->first()->tenant_id]);
            }

            return response()->json([
                'token' => $token,
                'user' => $user,
                'tenants' => $tenants
            ]);
        }

        throw new MessageExceptions([
            'Credenciales invalidas!',
        ]);
    }
}
