<?php

namespace App\handler;

use App\Exceptions\BaseException;
use Illuminate\Contracts\Validation\Validator;

class RequestValidatorHandler
{
    public function __invoke(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new BaseException("Form error", 400, $errors);
    }
}
