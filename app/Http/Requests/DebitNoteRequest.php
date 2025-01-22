<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use App\Models\DebitNote;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DebitNoteRequest extends FormRequest
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
            'debitNotes.*.fecha_emision' => ['required', 'max:80'],
            'debitNotes.*.fecha_autorizacion' => ['required', 'max:80'],
            'debitNotes.*.ruc_emisor' => ['required', 'max:13'],
            'debitNotes.*.clave_acceso' => ['required', 'max:49'],
            'debitNotes.*.razon_social_emisor' => ['required', 'max:255'],
            'debitNotes.*.nombre_comercial_emisor' => ['required', 'max:255'],
            'debitNotes.*.serie_comprobante' => ['required'],
            'debitNotes.*.direccion_matriz' => ['required'],
            'debitNotes.*.direccion_establecimiento' => ['required'],
            'debitNotes.*.tipo_identificacion_comprador' => ['required'],
            'debitNotes.*.identificacion_comprador' => ['required'],
            'debitNotes.*.contribuyente_especial' => ['nullable', 'max:20'],
            'debitNotes.*.obligado_llevar_contabilidad' => ['nullable', 'max:20'],
            'debitNotes.*.cod_doc_modificado' => ['required'],
            'debitNotes.*.num_doc_modificado' => ['required'],
            'debitNotes.*.fecha_emision_doc_modificado' => ['nullable', 'date'],
            'debitNotes.*.total_sin_impuestos' => ['required', 'numeric'],
            'debitNotes.*.valor_modificacion' => ['required', 'numeric'],
            'debitNotes.*.detalles' => ['required', 'array'],
            'debitNotes.*.detalles.*.codigo' => ['required', 'max:20'],
            'debitNotes.*.detalles.*.codigo_porcentaje' => ['nullable', 'max:20'],
            'debitNotes.*.detalles.*.descripcion' => ['required', 'max:255'],
            'debitNotes.*.detalles.*.cantidad' => ['required', 'numeric'],
            'debitNotes.*.detalles.*.precio_unitario' => ['required', 'numeric'],
            'debitNotes.*.detalles.*.descuento' => ['nullable', 'numeric'],
            'debitNotes.*.detalles.*.subtotal' => ['required', 'numeric'],
            'debitNotes.*.detalles.*.total_impuesto' => ['required', 'numeric'],
            'debitNotes.*.detalles.*.total' => ['required', 'numeric'],
            'debitNotes.*.valor_ice' => ['nullable', 'numeric'],
            'debitNotes.*.valor_irbpnr' => ['nullable', 'numeric'],
            'debitNotes.*.razones' => ['required', 'array'],
            'debitNotes.*.motivos_adicionales' => ['required', 'array'],
            'debitNotes.*.xmlUrl' => ['required', 'max:255'],
            'debitNotes.*.pdfUrl' => ['required', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function getDebitNoteFromRequest(): array
    {
        return $this->debitNotes;
    }
}
