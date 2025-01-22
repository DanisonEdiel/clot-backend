<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantUser extends Model
{
    use HasFactory;
    protected $table = 'tenant_user';

    protected $fillable = [
        'tenant_id',
        'role_id',
        'user_id'
    ];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
