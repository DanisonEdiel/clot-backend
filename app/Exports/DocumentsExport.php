<?php

namespace App\Exports;

use App\Services\GetVouchersService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DocumentsExport implements WithMultipleSheets
{
    private $tenantId;
    private $year;
    private $month;
    private $ruc;

    public function __construct($tenantId, $year, $month, $ruc)
    {
        $this->tenantId = $tenantId;
        $this->year = $year;
        $this->month = $month;
        $this->ruc = $ruc;
        set_time_limit(300);
    }

    public function sheets(): array
    {

        $sexo = new GetVouchersService();
        $vouchers  = $sexo->getVouchers($this->ruc, $this->month, $this->year, $this->tenantId);

        return [
            new IndexExport($this->tenantId, $this->ruc, $this->month, $this->year),
            new SummaryExport($vouchers,  $this->month, $this->year, $sexo->downloadedVouchers, $sexo->providers, $sexo->sriVouchers),
            new InvoiceExport($vouchers[0]['invoices'], $sexo->downloadedVouchers, $this->ruc, $this->tenantId),
            new CreditNoteExport($vouchers[2]['creditNotes'], $sexo->downloadedVouchers,$this->ruc, $this->tenantId),
            new DebitNoteExport($vouchers[3]['debitNotes'], $sexo->downloadedVouchers,$this->ruc, $this->tenantId),
            new RetentionExport($vouchers[1]['retentions'], $sexo->downloadedVouchers,$this->ruc, $this->tenantId)
        ];
    }
}
