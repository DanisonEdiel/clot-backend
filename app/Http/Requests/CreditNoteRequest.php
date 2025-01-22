<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use App\Models\CreditNote;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreditNoteRequest extends FormRequest
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
            'creditNotes.*.fecha_emision' => ['required', 'max:80'],
            'creditNotes.*.fecha_autorizacion' => ['required', 'max:80'],
            'creditNotes.*.ruc_emisor' => ['required', 'max:13'],
            'creditNotes.*.clave_acceso' => ['required', 'max:49'],
            'creditNotes.*.razon_social_emisor' => ['required', 'max:255'],
            'creditNotes.*.nombre_comercial_emisor' => ['required', 'max:255'],
            'creditNotes.*.serie_comprobante' => ['required'],
            'creditNotes.*.direccion_matriz' => ['required'],
            'creditNotes.*.direccion_establecimiento' => ['required'],
            'creditNotes.*.tipo_identificacion_comprador' => ['required'],
            'creditNotes.*.identificacion_comprador' => ['required'],
            'creditNotes.*.contribuyente_especial' => ['nullable', 'max:20'],
            'creditNotes.*.obligado_llevar_contabilidad' => ['nullable', 'max:20'],
            'creditNotes.*.cod_doc_modificado' => ['required'],
            'creditNotes.*.num_doc_modificado' => ['required'],
            'creditNotes.*.fecha_emision_doc_modificado' => ['nullable', 'date'],
            'creditNotes.*.total_sin_impuestos' => ['required', 'numeric'],
            'creditNotes.*.valor_modificacion' => ['required', 'numeric'],
            'creditNotes.*.motivo' => ['required'],
            'creditNotes.*.total_impuesto' => ['required', 'numeric'],
            'creditNotes.*.detalles' => ['required', 'array'],
            'creditNotes.*.detalles.*.codigo' => ['required', 'max:20'],
            'creditNotes.*.detalles.*.codigo_porcentaje' => ['nullable', 'max:20'],
            'creditNotes.*.detalles.*.descripcion' => ['required', 'max:255'],
            'creditNotes.*.detalles.*.cantidad' => ['required', 'numeric'],
            'creditNotes.*.detalles.*.precio_unitario' => ['required', 'numeric'],
            'creditNotes.*.detalles.*.descuento' => ['nullable', 'numeric'],
            'creditNotes.*.detalles.*.subtotal' => ['required', 'numeric'],
            'creditNotes.*.detalles.*.total_impuesto' => ['required', 'numeric'],
            'creditNotes.*.detalles.*.total' => ['required', 'numeric'],
            'creditNotes.*.valor_ice' => ['nullable', 'numeric'],
            'creditNotes.*.valor_irbpnr' => ['nullable', 'numeric'],
            'creditNotes.*.xmlUrl' => ['required'],
            'creditNotes.*.pdfUrl' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function getCreditNoteFromRequest(): array
    {
        return $this->creditNotes;
    }
}
