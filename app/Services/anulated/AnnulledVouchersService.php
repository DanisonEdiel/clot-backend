<?php

namespace App\Services\anulated;

use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnnulledVouchersService
{
    public function index(string $tenantId, string $ruc)
    {
        try {
            $response = Http::get(config('services.tygor_microservice_url.url') . '/denied-documents/get/' . $tenantId . '/' . $ruc);
            $data = [];
            if ($response->ok()){
                $vouchers = Voucher::where('tenant_id', $tenantId)->where('type', 'DNC')->get();
                foreach ($response->json() as $document) {
                    if ($vouchers->firstWhere('access_key', $document['accessKey'])) {
                        $document['is_downloaded'] = true;
                    } else {
                        $document['is_downloaded'] = false;
                    }
                    $data[] = $document;
                }
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function changeStatus(string $access_key, string $tenant_id)
    {
        try {
            $voucherInTygor = Voucher::where('tenant_id', $tenant_id)->where('type', 'DNC')->firstWhere('access_key', $access_key);
            $transaction = Transaction::firstWhere('tenant_id', $tenant_id);

            if (!$voucherInTygor) {
                Voucher::create([
                    'access_key' => $access_key,
                    'type' => 'DNC',
                    'tenant_id' => $tenant_id
                ]);
                $transaction->transactions = (int)$transaction->transactions - 1;
                $transaction->save();
            }

            return $voucherInTygor;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
