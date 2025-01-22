<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

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
        'contribuyente_especial',
        'obligado_llevar_contabilidad',
        'razon_social_comprador',
        'identificacion_comprador',
        'total_sin_impuestos',
        'total_descuento',
        'propina',
        'detalles',
        'importe_total',
        'valor_ice',
        'valor_irbpnr',
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
