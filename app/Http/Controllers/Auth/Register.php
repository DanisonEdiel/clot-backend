<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CreateNewRuc;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends Controller
{
    use PasswordValidationRules;

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'unique:tenants,email'],
            'phone' => ['nullable', 'numeric', 'digits:10'],
            'password' => [
                'required',
                Password::min(8)->numbers()
            ]
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        try {
            DB::beginTransaction();
            $tenant = Tenant::create([
                'email' => $request->email
            ]);

            $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' =>  Hash::make($request->password),
                    'phone' => $request->phone
                ]);

            DB::table('tenant_user')->insert([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role_id' => 1,
            ]);

            
            $subscription = Plan::firstWhere('is_started', true);
            CreateNewRuc::createSubscriptionAndTransactions($subscription ,$tenant);
            CreateNewRuc::createConfig($tenant);


            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            DB::commit();
            return response()->json([
                'token' => $token,
                'user' => $user,
                'tenants' => [$tenant]
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message', $e->getMessage()], 500);
        }
    }
}
