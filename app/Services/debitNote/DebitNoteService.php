<?php

namespace App\Services\debitNote;

use App\Interfaces\debitNote\DebitNoteRepository;
use App\Models\DebitNote;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DebitNoteService implements DebitNoteRepository
{
    public function create(array $debitNotes): void
    {
        try {
            foreach ($debitNotes as $debitNote) {
                if (DebitNote::where('tenant_id', $debitNote['tenant_id'])->where('clave_acceso', $debitNote['clave_acceso'])->exists()) {
                    continue;
                }
                $debitNote = new DebitNote($debitNote);
                $debitNote->save();
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }
    }

    public function getByRuc(string $ruc, string $tenantId)
    {
        try {
            $response = Http::get(config('services.tygor_microservice_url.url') . '/debit-note/get/' . $tenantId . '/' . $ruc);
            return json_decode($response);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
