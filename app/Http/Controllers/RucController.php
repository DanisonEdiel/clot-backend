<?php

namespace App\Http\Controllers;

use App\Events\FinishedSincronized;
use App\Events\RucCreated;
use App\Exceptions\MessageExceptions;
use App\Exceptions\ValidationException;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Invoice;
use App\Models\Retention;
use App\Models\Ruc;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RucController extends Controller
{
    public function store(Request $request)
    {
        set_time_limit(300);
        $validator = Validator::make(
            $request->all(),
            [
                'ruc' => [
                    'required',
                    Rule::unique('rucs', 'ruc')->where(function ($query) {
                        return $query->where('tenant_id', session('tenant_id'));
                    }),
                    'numeric',
                    'digits_between:10,13'
                ],
                'password' => ['required', 'string', 'max:50'],
            ]
        );

        $subscription = Subscription::where('tenant_id', $request->header('X-Tenant'))->first();

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (Ruc::where('tenant_id', $request->header('X-Tenant'))->count() >= $subscription->rucs) {
            throw new MessageExceptions([
                'La cantidad de rucs ha llegado al límite!',
            ]);
        }

        try {
            $ruc = new Ruc($request->all());
            $ruc->tenant_id = $request->header('X-Tenant');
            $state = true;
            $message = "Problemas con el SRI";
            try {
                $response = Http::timeout(1200)->post(config('services.tygor_microservice_url.url') . '/sri/validate-login', $ruc);
                $state = $response['state'];
                $message = $response['message'];
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }

            $ruc->wrongPassword = $state;
            $ruc->save();

            return response()->json([
                'ruc' => $ruc,
                'message' => $message
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function index(Request $request)
    {
        $rucs = Ruc::where('tenant_id', $request->header('X-Tenant'))->get();
        return response()->json($rucs, 200);
    }

    public function webhook(Request $request)
    {
        try {
            $ruc = Ruc::where('tenant_id', $request->tenant_id)->firstWhere('ruc', $request->ruc);
            $tenant = Tenant::find($request->tenant_id);
            $ruc->is_synchronizing = false;
            $ruc->save();
            if (!$tenant) {
                throw new MessageExceptions([
                    'Tenant not found!',
                ]);
            }
            return response()->json(event(new FinishedSincronized($tenant->email, "Sincronizacion finalizada")), 200);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        set_time_limit(300);
        $validator = Validator::make(
            $request->all(),
            [
                'ruc' => [
                    'required',
                    'numeric',
                    'digits_between:10,13',
                    Rule::unique('rucs', 'ruc')
                        ->where(function ($query) {
                            return $query->where('tenant_id', session('tenant_id'));
                        })
                        ->ignore($request->id)
                ],
                'password' => ['required', 'string', 'max:50'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $ruc = Ruc::find($request->id);
            if (!$ruc) {
                return response()->json([
                    'message' => 'Ruc not found'
                ], 400);
            }
            $state = $ruc->wrongPassword;
            $message = "Problemas con el SRI";
            try {
                $response = Http::timeout(1200)->post(config('services.tygor_microservice_url.url') . '/sri/validate-login', $request->all());
                $state = $response['state'];
                $message = $response['message'];
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }
            $ruc->wrongPassword = $state;
            $ruc->password = $request->password;
            $ruc->save();
            return response()->json([
                'ruc' => $ruc,
                'message' => $message
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        $ruc = Ruc::find($id);
        if (!$ruc) {
            return response()->json([
                'message' => 'Ruc not found'
            ], 400);
        }

        $vouchers = [
            Invoice::class,
            Retention::class,
            CreditNote::class,
            DebitNote::class
        ];

        try {
            foreach ($vouchers as $voucher) {
                if ($voucher::where('identificacion_comprador', $ruc->ruc)->exists()) {
                    return response()->json([
                        'message' => 'El ruc no puede ser borrado una vez consultados los comprobantes'
                    ], 400);
                }
            }
            $ruc->delete();
            return response()->json([], 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }
}
