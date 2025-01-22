<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'transactions',
        'bot_id',
        'tenant_id'
    ];

    public function addVoucher($vouchers)
    {
        $this->transactions += $vouchers;
    }
}
