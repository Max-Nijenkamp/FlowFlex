<?php

namespace App\Enums;

enum Language: string
{
    case EN = 'en';
    case NL = 'nl';
    case DE = 'de';

    public function label(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::NL => 'Dutch',
            self::DE => 'German',
        };
    }

    public function nativeLabel(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::NL => 'Nederlands',
            self::DE => 'Deutsch',
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::EN => '🇬🇧',
            self::NL => '🇳🇱',
            self::DE => '🇩🇪',
        };
    }
}
