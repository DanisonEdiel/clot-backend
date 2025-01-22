<?php

namespace App\Http\Controllers\Bot;

use App\Events\FinishedSincronized;
use App\Exceptions\MessageExceptions;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Ruc;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BotController extends Controller
{
    public function sendRucToQueue(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => ['required', 'numeric'],
                'month' => ['required', 'numeric', 'between:1,12'],
                'year' => ['required', 'numeric', 'digits:4'],
                'documents' => ['required'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        try {
            DB::beginTransaction();
            $ruc = Ruc::find($request->id);
            $data = [
                'ruc' => $ruc,
                'month' => $request->month,
                'year' => $request->year,
                'documents' => $request->documents,
                'webhook' => config('services.app-url.url') . "/api/ruc/webhook"
            ];
            $response = Http::post(config('services.tygor_microservice_url.url') . '/sri/synchronize', $data);
            $ruc->is_synchronizing = true;
            $ruc->save();
            DB::commit();
            return response()->json([
                'message' => 'RUC enviado a la cola'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendTenantRucsToQueue(Request $request)
    {
        try {
            $ruc = Ruc::find($request->id);
            // event(new TenantRucsSend($ruc));
            return response()->json([
                'message' => 'RUC enviados a la cola'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function wrongPassword(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'state' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $ruc = Ruc::find($id);
            if (!$ruc) {
                throw new MessageExceptions([
                    'Ruc not found!',
                ]);
            }
            $ruc->wrongPassword = $request->state;
            $ruc->save();
            return response()->json([
                'message' => 'Ruc updated'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function setSync(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'state' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            $ruc = Ruc::find($id);
            if (!$ruc) {
                throw new MessageExceptions([
                    'Ruc not found!',
                ]);
            }
            $ruc->is_synchronizing = $request->state;
            $ruc->save();
            return response()->json([
                'message' => 'Ruc updated'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenantId' => ['required'],
            'message' => ['required', 'max:500']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $tenant = Tenant::find($request->tenantId);

        if (!$tenant) {
            throw new MessageExceptions([
                'Tenant not found!',
            ]);
        }
        return event(new FinishedSincronized($tenant->email, $request->message));
    }
}
