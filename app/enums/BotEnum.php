<?php

namespace App\enums;

enum BotEnum
{
    case TYGOR;
    public function token(): string
    {
        return match ($this) {
            BotEnum::TYGOR => '1d85833b-3f00-4d9c-a5bc-c9b972da063e',
        };
    }
}
