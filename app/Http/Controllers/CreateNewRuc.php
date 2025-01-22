<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageExceptions;
use App\Exceptions\ValidationException;
use App\Mail\InviteUserToTenant;
use App\Models\User;
use App\Models\Config;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateNewRuc extends Controller
{
    public function createNewRuc(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user.name' => ['required', 'string', 'max:255'],
                'user.email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'subscription.vouchers' => ['required', 'string', 'min:1'],
                'subscription.usersOnline' => ['required', 'min:1'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $tenant = Tenant::where('email', $request->user['email'])->first();
            DB::beginTransaction();

            if (!$tenant) {
                $tenant = $this->createTenant($request->user['email']);
                $user = $this->createUser($request->user);
                $this->createSubscriptionAndTransactions($request->subscription, $tenant);
                $this->createConfig($tenant);
                DB::table('tenant_user')->insert([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'role_id' => 1,
                ]);
            } else {
                $subscription = Subscription::where('tenant_id', $tenant->id)->first();
                $subscription->update([
                    'vouchers' => $request->subscription['vouchers'],
                    'usersOnline' => $request->subscription['usersOnline']
                ]);
                $transaction = Transaction::where('tenant_id', $tenant->id)->where('bot_id', 1)->first();
                $transaction->transactions = intval($transaction->transactions) + intval($request->subscription['vouchers']);
                $transaction->update();
            }

            DB::commit();

            throw new MessageExceptions([
                'Account sucessfully created!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    private function createTenant($email)
    {
        try {
            $tenant = new Tenant();
            $tenant->email = $email;
            $tenant->save();

            return $tenant;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    private function createUser($user)
    {
        try {
            $temporalPasswords = Str::random(15);
            $newUser = new User();
            $newUser->name = $user['name'];
            $newUser->email = $user['email'];
            $newUser->password = $temporalPasswords;
            $newUser->save();
            $correo = new InviteUserToTenant($newUser->email, $temporalPasswords);
            Mail::to($newUser->email)->send($correo);
            return $newUser;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public static function createSubscriptionAndTransactions($subscription, $tenant)
    {
        try {
            $newSubscription = new Subscription();
            $newSubscription->vouchers = $subscription->vouchers;
            $newSubscription->usersOnline = $subscription->usersOnline;
            $newSubscription->tenant_id = $tenant->id;
            $newSubscription->rucs = $subscription->rucs;
            $newSubscription->plan_id = $subscription->id;
            $newSubscription->save();

            $newTransactions = new Transaction();
            $newTransactions->transactions = $subscription->vouchers;
            $newTransactions->tenant_id = $tenant->id;
            $newTransactions->bot_id = 1;
            $newTransactions->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public static function createConfig($tenant)
    {
        try {
            $newConfig = new Config();
            $newConfig->concurrency = 'd';
            $newConfig->documents = json_encode(['Facturas']);
            $newConfig->tenant_id = $tenant->id;
            $newConfig->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
