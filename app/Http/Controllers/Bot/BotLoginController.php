<?php

namespace App\Http\Controllers\Bot;

use App\Exceptions\MessageExceptions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class BotLoginController extends Controller
{
    public function botLogin(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw new MessageExceptions([
                'Invalid credentials!',
            ]);
        }
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->delete();
        if (
            $user &&
            Hash::check($request->password, $user->password)
        ) {
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->update(['expires_at' => now()->addHours(8)]);

            return response()->json([
                'token' => $token,
            ]);
        } else {
            throw new MessageExceptions([
                'Invalid credentials!',
            ]);
        }
    }
}
