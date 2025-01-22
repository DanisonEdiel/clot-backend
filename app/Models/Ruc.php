<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruc extends Model
{
    use HasFactory;

    protected $table = 'rucs';

    protected $fillable = [
        'id',
        'name',
        'ruc',
        'password',
        'wrongPassword',
        'is_synchronizing',
        'tenant_id',
    ];
}
