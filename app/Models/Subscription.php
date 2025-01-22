<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'rucs',
        'price',
        'vouchers',
        'usersOnline',
        'tenant_id',
        'plan_id',
    ];

    public function plan(){
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
