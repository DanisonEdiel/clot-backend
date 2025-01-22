<?php

namespace App\Interfaces\retention;

use App\Models\Retention;

interface RetentionRepository
{
    public function create(array $retentions);
    public function getByRuc(string $ruc, string $tenantId);
}
