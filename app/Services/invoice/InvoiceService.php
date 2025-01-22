<?php

namespace App\Services\invoice;

use App\Interfaces\invoice\InvoiceRepository;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InvoiceService implements InvoiceRepository
{
    public function create(array $invoices): void
    {
        try {
            foreach ($invoices as $invoice) {
                if (Invoice::where('tenant_id', $invoice['tenant_id'])->where('clave_acceso', $invoice['clave_acceso'])->exists()) {
                    continue;
                }
                $invoice = new Invoice($invoice);
                $invoice->save();
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }
    }

    public function getByRuc(string $ruc, string $tenantId)
    {
        try {

            $response = Http::get(config('services.tygor_microservice_url.url') . '/invoices/get/' . $tenantId . '/' . $ruc);
            return json_decode($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
