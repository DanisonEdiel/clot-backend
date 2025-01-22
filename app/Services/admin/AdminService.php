<?php

namespace App\Services\admin;

use App\enums\BotEnum;
use App\Exceptions\MessageExceptions;
use App\Interfaces\admin\AdminRepository;
use App\Mail\InviteUserToTenant;
use App\Models\Admin;
use App\Models\Bot;
use App\Models\Deposit;
use App\Models\Plan;
use App\Models\Ruc;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminService implements AdminRepository
{
    public function login(string $email, string $password)
    {
        $admin = Admin::firstWhere('email', strtolower($email));
        if (!$admin) {
            throw new MessageExceptions([
                'Credenciales invalidas!',
            ]);
        }

        $this->logout($admin->id);

        if (Hash::check($password, $admin->password)) {
            $token = $admin->createToken($admin->name . '-AuthToken')->plainTextToken;

            return [
                'token' => $token,
                'admin' => $admin,
            ];
        }

        throw new MessageExceptions([
            'Credenciales invalidas!',
        ]);
    }

    public function logout(int $id)
    {
        try {
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $id)
                ->delete();
        } catch (\Exception $e) {
            throw new MessageExceptions([
                'Error al cerrar sesión' => $e->getMessage()
            ]);
        }

    }

    public function dashboard()
    {
        try {
            $tenants = Tenant::with('owner')->get();
            $companies = [];
            foreach ($tenants as $tenant) {
                $rucs = Ruc::where('tenant_id', $tenant->id)->get()->pluck('tenant_id')->count();
                $subscription = Subscription::firstWhere('tenant_id', $tenant->id);
                $plan = Plan::find($subscription->plan_id)->name;
                $transactions = Transaction::firstWhere('tenant_id', $tenant->id)->transactions;
                $companies[] = [
                    'company' => $tenant,
                    'rucs' => $rucs,
                    'plan' => $plan,
                    'vouchers' => $subscription->vouchers,
                    'downloadedVouchers' => $transactions
                ];
            }
            return $companies;
        } catch (\Exception $e) {
            throw new MessageExceptions([
                'Error al obtener el dashboard' => $e->getMessage()
            ]);
        }
    }

    public function changePlan(Deposit $deposit, $file)
    {
        try {


            $plan = Plan::find($deposit->plan_id);
            $admin = Admin::find($deposit->admin_id)->name;
            $deposit->plan = $plan->name;
            $deposit->user = $admin;

            $customFileName = Str::slug(now()) . $deposit->tenant_id . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/vouchers/' . $deposit->tenant_id, $customFileName);

            $deposit->voucher = 'vouchers/' . $deposit->tenant_id . '/' . $customFileName;

            $deposit->save();

            $subscription = Subscription::firstWhere('tenant_id', $deposit->tenant_id);

            $subscription->plan_id = $plan->id;

            $bot = Bot::firstWhere('token', BotEnum::TYGOR->token());

            $transaction = Transaction::where('tenant_id', $deposit->tenant_id)->where('bot_id', $bot->id)->first();

            $transaction->addVoucher($plan->vouchers);

            $transaction->save();

            $subscription->rucs = $plan->rucs;

            $subscription->usersOnline = $plan->usersOnline;

            $subscription->vouchers = $plan->vouchers;

            $subscription->save();


            return response()->json([
                "message" => "Payment processed successfully."
            ], 200);
        } catch (\Exception $e) {
            throw new MessageExceptions([
                'Error al cambiar el plan' => $e->getMessage()
            ]);
        }

    }

    public function showCompany(string $tenantId)
    {
        try {
            $tenant = Tenant::with('owner', 'ruc', 'subscription.plan', 'transactions', 'payment', 'deposit')->where('id', $tenantId)->first();
            return $tenant;
        } catch (\Exception $e) {
            throw new MessageExceptions([
                'Error al obtener la información de la empresa' => $e->getMessage()
            ]);
        }
    }

    public function addNewAdmin(string $email, string $name, int $adminId)
    {
        try {

            if (!Admin::find($adminId)->exists()) {
                throw new MessageExceptions([
                    'No puedes realizar esta operación mmwebaso!'
                ]);
            }

            $admin = Admin::where('email', strtolower($email))->first();
            if ($admin) {
                throw new MessageExceptions([
                    'Administrador ya existe!'
                ]);
            }
            $newAdmin = new Admin();
            $newAdmin->name = $name;
            $newAdmin->email = strtolower($email);
            $temporalPassword = Str::random(16);
            $newAdmin->password = Hash::make($temporalPassword);
            $newAdmin->save();

            $mail = new InviteUserToTenant($email, $temporalPassword);
            Mail::to($email)->send($mail);

            return $newAdmin;
        } catch (\Exception $e) {
            throw new MessageExceptions([
                'Error al crear nuevo administrador' => $e->getMessage()
            ]);
        }
    }


}
