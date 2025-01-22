<?php

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    private array $errors;

    public function __construct(string $message, int $code, array $errors)
    {
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
