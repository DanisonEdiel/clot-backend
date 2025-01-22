<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use App\Models\Retention;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RetentionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'retentions.*.fecha_emision' => ['required', 'max:80'],
            'retentions.*.fecha_autorizacion' => ['required', 'max:80'],
            'retentions.*.ruc_emisor' => ['required', 'max:13'],
            'retentions.*.clave_acceso' => ['required', 'max:49'],
            'retentions.*.razon_social_emisor' => ['required', 'max:255'],
            'retentions.*.nombre_comercial_emisor' => ['required', 'max:255'],
            'retentions.*.serie_comprobante' => ['required'],
            'retentions.*.direccion_matriz' => ['required'],
            'retentions.*.direccion_establecimiento' => ['required'],
            'retentions.*.tipo_identificacion_comprador' => ['required'],
            'retentions.*.tipo_identificacion_sujeto_retention' => ['required'],
            'retentions.*.identificacion_comprador' => ['required'],
            'retentions.*.contribuyente_especial' => ['nullable', 'max:20'],
            'retentions.*.obligado_llevar_contabilidad' => ['nullable', 'max:20'],
            'retentions.*.tipo_identificacion_sujeto_retenido' => ['required', 'max:255'],
            'retentions.*.razon_social_sujeto_retenido' => ['required', 'max:255'],
            'retentions.*.identificacion_sujeto_retenido' => ['required', 'max:255'],
            'retentions.*.periodo_fiscal' => ['required'],
            'retentions.*.total_retenido' => ['required', 'numeric'],
            'retentions.*.detalles' => ['required', 'array'],
            'retentions.*.detalles.*.codigo_impuesto' => ['required'],
            'retentions.*.detalles.*.codigo_porcentaje' => ['required'],
            'retentions.*.detalles.*.codigo_retencion' => ['required'],
            'retentions.*.detalles.*.base_imponible' => ['required', 'numeric'],
            'retentions.*.detalles.*.porcentaje_retencion' => ['required', 'numeric'],
            'retentions.*.detalles.*.valor_retenido' => ['required', 'numeric'],
            'retentions.*.detalles.*.cod_documento_sustento' => ['required', 'numeric'],
            'retentions.*.detalles.*.num_documento_sustento' => ['required', 'numeric'],
            'retentions.*.detalles.*.fecha_emision_sustento' => ['required', 'date:Y-m-d'],
            'retentions.*.detalles.*.fecha_registro_contable' => ['nullable', 'numeric'],
            'retentions.*.detalles.*.num_aut_doc_sustento' => ['nullable', 'numeric'],
            'retentions.*.xmlUrl' => ['nullable'],
            'retentions.*.pdfUrl' => ['nullable'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function getRetentionFromRequest(): array
    {
        return $this->retentions;
    }
}
