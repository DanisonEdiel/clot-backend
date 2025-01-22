<?php

namespace App\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as BaseValidationException;

class ValidationException extends BaseValidationException
{
    public function __construct(Validator $validator)
    {
        parent::__construct($validator);
    }

    public function render()
    {
        $messages = $this->validator->errors()->all();

        return response()->json([
            'message' => $messages
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
