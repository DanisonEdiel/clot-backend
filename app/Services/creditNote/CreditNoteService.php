<?php

namespace App\Services\creditNote;

use App\Interfaces\creditNote\CreditNoteRepository;
use App\Models\CreditNote;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreditNoteService implements CreditNoteRepository
{
    public function create(array $creditNotes): void
    {
        try {
            foreach ($creditNotes as $creditNote) {
                if (CreditNote::where('tenant_id', $creditNote['tenant_id'])->where('clave_acceso', $creditNote['clave_acceso'])->exists()) {
                    continue;
                }
                $creditNote = new CreditNote($creditNote);
                $creditNote->save();
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }
    }

    public function getByRuc(string $ruc, string $tenantId)
    {
        try {
            $response = Http::get(config('services.tygor_microservice_url.url') . '/credit-note/get/' . $tenantId . '/' . $ruc);
            return json_decode($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
