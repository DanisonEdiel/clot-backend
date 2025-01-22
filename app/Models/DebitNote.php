<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    use HasFactory;

    protected $table = 'debit_notes';

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
        'cod_doc_modificado',
        'num_doc_modificado',
        'fecha_emision_doc_modificado',
        'total_sin_impuestos',
        'valor_modificacion',
        'detalles',
        'valor_ice',
        'valor_irbpnr',
        'razones',
        'motivos_adicionales',
        'xmlUrl',
        'pdfUrl',
        'is_downloaded',
        'tenant_id'
    ];

    public function setDetallesAttribute($value)
    {
        $this->attributes['detalles'] = json_encode($value);
    }
    public function setRazonesAttribute($value)
    {
        $this->attributes['razones'] = json_encode($value);
    }
    public function setMotivosAdicionalesAttribute($value)
    {
        $this->attributes['motivos_adicionales'] = json_encode($value);
    }
}
