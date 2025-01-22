<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'lastDigits',
        'clientTransactionId',
        'transactionId',
        'phoneNumber',
        'email',
        'cardType',
        'transactionStatus',
        'authorizationCode',
        'date',
        'plan_id',
        'tenant_id',
    ];
}
