<?php

namespace App\Enums\Hr;

enum CustomFieldType: string
{
    case Text     = 'text';
    case Number   = 'number';
    case Date     = 'date';
    case Dropdown = 'dropdown';
    case Checkbox = 'checkbox';

    public function label(): string
    {
        return match($this) {
            self::Text     => 'Text',
            self::Number   => 'Number',
            self::Date     => 'Date',
            self::Dropdown => 'Dropdown',
            self::Checkbox => 'Checkbox',
        };
    }
}
