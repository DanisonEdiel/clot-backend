<?php

namespace App\Interfaces\invoice;

use App\Models\Invoice;

interface InvoiceRepository
{
    public function create(array $invoices);
    public function getByRuc(string $ruc, string $tenantId);
}
