<?php

namespace App\Services;

use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Invoice;
use App\Models\Retention;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetVouchersService
{
    public int $downloadedVouchers = 0, $sriVouchers = 0, $providers = 0;

    private array $registerProvider = [];

    public function getVouchers(string $ruc, $month, $year, $tenantId): array
    {
        $data = [
            'ruc' => $ruc,
            'month' => $month,
            'year' => $year,
            'tenant_id' => $tenantId,
        ];

        $vouchers = [
            'invoices' => '/invoices/range',
            'retentions' => '/retentions/range',
            'creditNotes' => '/credit-note/range',
            'debitNotes' => '/debit-note/range',
        ];

        $transaction = Transaction::firstWhere('tenant_id', $tenantId);

        foreach ($vouchers as $voucherName => $voucherType) {
            $documents = Http::post(config('services.tygor_microservice_url.url') . $voucherType, $data);

            $invoices = [];

            foreach (json_decode($documents) as $document) {
                $voucherInTygor = Voucher::where('tenant_id', $transaction->tenant_id)->where('type', $this->getWord($voucherName))->firstWhere('access_key', $document->clave_acceso);
                if (!$voucherInTygor) {
                    $this->sriVouchers++;
                    if ($transaction->transactions > 0) {
                        $transaction->transactions = (int)$transaction->transactions - 1;
                        $transaction->save();
                        Voucher::create([
                            'access_key' => $document->clave_acceso,
                            'type' => $this->getWord($voucherName),
                            'tenant_id' => $transaction->tenant_id
                        ]);
                        $this->downloadedVouchers++;
                        $this->containsProvider($document->ruc_emisor);
                        $invoices[] = $document;
                    }
                } else {
                    $this->containsProvider($document->ruc_emisor);

                    $this->sriVouchers++;
                    $this->downloadedVouchers++;
                    $invoices[] = $document;
                }
            }
            $series[] = [
                $voucherName => $invoices,
            ];
        }
        return $series;
    }

    private function containsProvider($ruc)
    {
        if (!in_array($ruc, $this->registerProvider)) {
            array_push($this->registerProvider, $ruc);
            $this->providers++;
        }
    }

    private function getWord($word)
    {
        switch ($word) {
            case 'invoices':
                return 'FAC';
            case'retentions':
                return 'RET';
            case 'creditNotes':
                return 'NC';
            case 'debitNotes':
                return 'ND';
            default:
                return 'DOC';
        }
    }
}
