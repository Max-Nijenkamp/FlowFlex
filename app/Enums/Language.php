<?php

namespace App\Enums;

enum Language: string
{
    case EN = 'en';
    case NL = 'nl';
    case DE = 'de';
    case FR = 'fr';
    case ES = 'es';

    public function label(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::NL => 'Dutch',
            self::DE => 'German',
            self::FR => 'French',
            self::ES => 'Spanish',
        };
    }

    public function nativeLabel(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::NL => 'Nederlands',
            self::DE => 'Deutsch',
            self::FR => 'Français',
            self::ES => 'Español',
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::EN => '🇬🇧',
            self::NL => '🇳🇱',
            self::DE => '🇩🇪',
            self::FR => '🇫🇷',
            self::ES => '🇪🇸',
        };
    }
}
