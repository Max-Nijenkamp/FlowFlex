<?php

namespace App\Enums;

enum Country: string
{
    case NL = 'NL';
    case GB = 'GB';

    public function label(): string
    {
        return match ($this) {
            self::NL => 'Netherlands',
            self::GB => 'English',
        };
    }
}
