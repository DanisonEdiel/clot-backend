<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class InvoiceRequest extends FormRequest
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
            'invoices' => ['required', 'array'],
            'invoices.*.fecha_emision' => ['required', 'max:80'],
            'invoices.*.fecha_autorizacion' => ['required', 'max:80'],
            'invoices.*.ruc_emisor' => ['required', 'max:13'],
            'invoices.*.clave_acceso' => ['required', 'max:49'],
            'invoices.*.razon_social_emisor' => ['required', 'max:255'],
            'invoices.*.nombre_comercial_emisor' => ['required', 'max:255'],
            'invoices.*.serie_comprobante' => ['required'],
            'invoices.*.direccion_matriz' => ['required'],
            'invoices.*.direccion_establecimiento' => ['required'],
            'invoices.*.tipo_identificacion_comprador' => ['required'],
            'invoices.*.contribuyente_especial' => ['nullable', 'max:20'],
            'invoices.*.obligado_llevar_contabilidad' => ['nullable', 'max:20'],
            'invoices.*.razon_social_comprador' => ['required', 'max:255'],
            'invoices.*.identificacion_comprador' => ['required', 'max:13'],
            'invoices.*.total_sin_impuestos' => ['required', 'numeric'],
            'invoices.*.total_descuento' => ['required', 'numeric'],
            'invoices.*.propina' => ['required', 'numeric'],
            'invoices.*.detalles' => ['required','array'],
            'invoices.*.detalles.*.codigo' => ['required', 'max:20'],
            'invoices.*.detalles.*.codigo_porcentaje' => ['nullable', 'max:20'],
            'invoices.*.detalles.*.descripcion' => ['required', 'max:255'],
            'invoices.*.detalles.*.cantidad' => ['required', 'numeric'],
            'invoices.*.detalles.*.precio_unitario' => ['required', 'numeric'],
            'invoices.*.detalles.*.descuento' => ['nullable', 'numeric'],
            'invoices.*.detalles.*.subtotal' => ['required', 'numeric'],
            'invoices.*.detalles.*.total_impuesto' => ['required', 'numeric'],
            'invoices.*.detalles.*.total' => ['required', 'numeric'],
            'invoices.*.importe_total' => ['required', 'numeric'],
            'invoices.*.valor_ice' => ['required', 'numeric'],
            'invoices.*.valor_irbpnr' => ['required', 'numeric'],
            'invoices.*.xmlUrl' => ['required'],
            'invoices.*.pdfUrl' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function getInvoiceFromRequest(): array
    {
        return $this->invoices;
    }
}
