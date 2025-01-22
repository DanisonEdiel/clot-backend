<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'email'
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'tenant_id');
    }

    public function config()
    {
        return $this->hasOne(Config::class, 'tenant_id');
    }

    public function transactions()
    {
        return $this->hasOne(Transaction::class, 'tenant_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    public function ruc(){
        return $this->hasMany(Ruc::class);
    }

    public function payment(){
        return $this->hasMany(Payment::class);
    }

    public function deposit(){
        return $this->hasMany(Deposit::class);
    }
}
