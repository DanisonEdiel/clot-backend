<?php

namespace App\Interfaces\creditNote;

use App\Models\CreditNote;

interface CreditNoteRepository
{
    public function create(array $creditNotes);
    public function getByRuc(string $ruc, string $tenantId);
}
