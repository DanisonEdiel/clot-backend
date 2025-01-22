<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Bot;
use App\Models\Plan;
use App\enums\BotEnum;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function findPlan(Request $request)
    {

        if (!$request->id) {
            return response()->json(['message' => 'Plan ID is required.'], 400);
        }
        try {
            $plan = Plan::find($request->id);

            $amount = round(floatval($plan->price) * 100);

            do {
                $transactionId = Str::random(40);
            } while (Payment::where('clientTransactionId', $transactionId)->exists());

            return response()->json([
                'token' => config('services.payment.token'),
                'transactionId' => $transactionId,
                'amount' => $amount,

            ]);
        } catch (Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function addPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "product_id" => "required",
            "payment" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()
            ], 400);
        }

        try {
            $plan = Plan::find($request->product_id);

            $payment = new Payment();

            $payment->lastDigits = $request->payment['lastDigits'];
            $payment->clientTransactionId = $request->payment['clientTransactionId'];
            $payment->transactionId = $request->payment['transactionId'];
            $payment->phoneNumber = $request->payment['phoneNumber'] ?? 'No phone number available';
            $payment->email = $request->payment['email'] ?? 'No email available';
            $payment->cardType = $request->payment['cardType'];
            $payment->transactionStatus = $request->payment['transactionStatus'];
            $payment->authorizationCode = $request->payment['authorizationCode'];
            $payment->amount = $request->payment['amount'];
            $payment->date = $request->payment['date'];
            $payment->plan_id = $plan->id;
            $payment->tenant_id = $request->header('X-Tenant');
            $payment->save();

            if ($request->payment['statusCode'] == 3) {
                $subscription = Subscription::firstWhere('tenant_id', $request->header('X-Tenant'));

                $bot = Bot::firstWhere('token', BotEnum::TYGOR->token());

                $transaction = Transaction::where('tenant_id', $request->header('X-Tenant'))->where('bot_id', $bot->id)->first();

                $transaction->addVoucher($plan->vouchers);

                $transaction->save();

                $subscription->rucs = $plan->rucs;

                $subscription->plan_id = $plan->id;

                $subscription->usersOnline = $plan->usersOnline;

                $subscription->vouchers = $plan->vouchers;

                $subscription->save();
            }
            return response()->json([
                "message" => "Payment processed successfully."
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }
}
