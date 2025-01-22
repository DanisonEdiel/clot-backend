<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use App\Models\Deposit;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'file' => ['required'],
            'plan_id' => ['required'],
            'tenant_id' => ['required'],
            'admin_id' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function getDepositFromRequest(): Deposit
    {
        return new Deposit($this->all());
    }
}
