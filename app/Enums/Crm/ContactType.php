<?php

namespace App\Enums\Crm;

enum ContactType: string
{
    case Lead     = 'lead';
    case Prospect = 'prospect';
    case Customer = 'customer';
    case Partner  = 'partner';
    case Other    = 'other';

    public function label(): string
    {
        return match($this) {
            self::Lead     => 'Lead',
            self::Prospect => 'Prospect',
            self::Customer => 'Customer',
            self::Partner  => 'Partner',
            self::Other    => 'Other',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Lead     => 'warning',
            self::Prospect => 'info',
            self::Customer => 'success',
            self::Partner  => 'primary',
            self::Other    => 'gray',
        };
    }
}
