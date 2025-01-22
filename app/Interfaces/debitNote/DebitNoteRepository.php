<?php

namespace App\Interfaces\debitNote;

use App\Models\DebitNote;

interface DebitNoteRepository
{
    public function create(array $debitNotes);
    public function getByRuc(string $ruc, string $tenantId);
}
