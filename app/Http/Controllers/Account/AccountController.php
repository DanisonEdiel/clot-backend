<?php

namespace App\Http\Controllers\Account;

use App\Exceptions\MessageExceptions;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class AccountController extends Controller implements UpdatesUserProfileInformation
{

    public function show(string $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            throw new MessageExceptions([
                'User not found!',
            ]);
        }

        return response()->json($user, 200);
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            throw new MessageExceptions([
                'User not found!',
            ]);
        }

        $user->update($request->all());
        return response()->json($user, 204);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new MessageExceptions([
                'User not found!',
            ]);
        }

        try {
            DB::beginTransaction();
            DB::table('tenant_user')->where('user_id', $id)->delete();
            $user->delete();
            DB::commit();
            return response()->json(204);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function updateAccountPhoto(Request $request)
    {
        try {
            $user = User::find($request->userId);

            if(!$user){
                throw new MessageExceptions([
                    '¡Usuario no encontrado!',
                ]);
            }

            $user->updateProfilePhoto($request->photo);
            return response()->json($user, 200);
        } catch (\Throwable $th) {
            return response()->json(['message', $th->getMessage()], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            set_time_limit(120);
            $request->validate(
                [
                    'email' => 'required|email'
                ]
            );

            $user = User::where('email', strtolower($request->email))->first();
            if (!$user) {
                throw new MessageExceptions([
                    '¡Usuario no encontrado!',
                ]);
            }

            $temporalPassword = Str::random(15);
            $user->password = Hash::make($temporalPassword);
            $user->save();

            $mail = new ResetPasswordMail($temporalPassword);
            Mail::to($request->email)->send($mail);

            return response()->json(200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updatePasswordAccount(Request $request)
    {
        try {
            $request->validate([
                'newPassword' => 'required',
                'oldPassword' => 'required',
                'userId' => 'required',
            ]);
            $user = User::find($request->userId);
            if (!$user) {
                throw new MessageExceptions([
                    'User not found!',
                ]);
            }
            if (
                !Hash::check($request->oldPassword, $user->password)
            ) {
                throw new MessageExceptions([
                    'Contraseña anterior incorrecta!',
                ]);
            }
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return response()->json($user, 200);
        } catch (\Throwable $th) {
            return response()->json(['message', $th->getMessage()], 401);
        }
    }
}
