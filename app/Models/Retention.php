<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retention extends Model
{
    use HasFactory;

    protected $table = 'retentions';

    protected $fillable = [
        'fecha_emision',
        'fecha_autorizacion',
        'ruc_emisor',
        'clave_acceso',
        'razon_social_emisor',
        'nombre_comercial_emisor',
        'serie_comprobante',
        'direccion_matriz',
        'direccion_establecimiento',
        'tipo_identificacion_comprador',
        'identificacion_comprador',
        'contribuyente_especial',
        'obligado_llevar_contabilidad',
        'tipo_identificacion_sujeto_retention',
        'razon_social_sujeto_retenido',
        'identificacion_sujeto_retenido',
        'periodo_fiscal',
        'total_retenido',
        'detalles',
        'xmlUrl',
        'pdfUrl',
        'is_downloaded',
        'tenant_id'
    ];

    public function setDetallesAttribute($value)
    {
        $this->attributes['detalles'] = json_encode($value);
    }
}
