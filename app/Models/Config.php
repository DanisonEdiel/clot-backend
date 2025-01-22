<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $table = 'config';

    protected $fillable = [
        'sriPassword',
        'concurrency',
        'documents',
        'customDay',
        'customMonth',
        'tenant_id',
    ];

    public function getDocumentsAttribute($value){
        return json_decode($value, true);
    }
}
