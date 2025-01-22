<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $table = 'deposits';

    protected $fillable = [
        'plan',
        'user',
        'voucher',
        'plan_id',
        'tenant_id',
        'admin_id'
    ];


}
