<?php

namespace App\Services\retention;

use App\Interfaces\retention\RetentionRepository;
use App\Models\Retention;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RetentionService implements RetentionRepository
{
    public function create(array $retentions): void
    {
        try {
            foreach ($retentions as $retention) {
                if (Retention::where('tenant_id', $retention['tenant_id'])->where('clave_acceso', $retention['clave_acceso'])->exists()) {
                    continue;
                }
                $retention = new Retention($retention);
                $retention->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function getByRuc(string $ruc, string $tenantId)
    {
        try {
            $response = Http::get(config('services.tygor_microservice_url.url') . '/retentions/get/' . $tenantId . '/' . $ruc);
            return json_decode($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
