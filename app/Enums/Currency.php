<?php

namespace App\Enums;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case CHF = 'CHF';
    case JPY = 'JPY';
    case AUD = 'AUD';
    case CAD = 'CAD';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';
    case HUF = 'HUF';
    case RON = 'RON';
    case BGN = 'BGN';
    case TRY = 'TRY';
    case AED = 'AED';
    case SGD = 'SGD';
    case HKD = 'HKD';
    case INR = 'INR';
    case ZAR = 'ZAR';
    case BRL = 'BRL';
    case MXN = 'MXN';

    public function label(): string
    {
        return match ($this) {
            self::EUR => 'Euro',
            self::USD => 'US Dollar',
            self::GBP => 'British Pound',
            self::CHF => 'Swiss Franc',
            self::JPY => 'Japanese Yen',
            self::AUD => 'Australian Dollar',
            self::CAD => 'Canadian Dollar',
            self::SEK => 'Swedish Krona',
            self::NOK => 'Norwegian Krone',
            self::DKK => 'Danish Krone',
            self::PLN => 'Polish Złoty',
            self::CZK => 'Czech Koruna',
            self::HUF => 'Hungarian Forint',
            self::RON => 'Romanian Leu',
            self::BGN => 'Bulgarian Lev',
            self::TRY => 'Turkish Lira',
            self::AED => 'UAE Dirham',
            self::SGD => 'Singapore Dollar',
            self::HKD => 'Hong Kong Dollar',
            self::INR => 'Indian Rupee',
            self::ZAR => 'South African Rand',
            self::BRL => 'Brazilian Real',
            self::MXN => 'Mexican Peso',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::USD => '$',
            self::GBP => '£',
            self::CHF => 'CHF',
            self::JPY => '¥',
            self::AUD => 'A$',
            self::CAD => 'C$',
            self::SEK => 'kr',
            self::NOK => 'kr',
            self::DKK => 'kr',
            self::PLN => 'zł',
            self::CZK => 'Kč',
            self::HUF => 'Ft',
            self::RON => 'lei',
            self::BGN => 'лв',
            self::TRY => '₺',
            self::AED => 'د.إ',
            self::SGD => 'S$',
            self::HKD => 'HK$',
            self::INR => '₹',
            self::ZAR => 'R',
            self::BRL => 'R$',
            self::MXN => '$',
        };
    }
}
