<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class MessageExceptions extends Exception
{
    protected $messages;

    public function __construct(array $messages)
    {
        parent::__construct();
        $this->messages = $messages;
    }

    public function render()
    {
        return response()->json([
            'message' => $this->messages
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
