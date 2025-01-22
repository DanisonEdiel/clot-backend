<?php

namespace App\Exceptions;

use Exception;

class ExceptionWrapper
{
    public string $message;
    public array $errors;


    public function __construct(string $message, array $errors)
    {
        $this->message = $message;
        $this->errors = $errors;
    }
}
