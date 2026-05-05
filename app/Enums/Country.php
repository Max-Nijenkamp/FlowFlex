<?php

namespace App\Enums;

enum Country: string
{
    case AT = 'AT';
    case AU = 'AU';
    case BE = 'BE';
    case CA = 'CA';
    case CH = 'CH';
    case CZ = 'CZ';
    case DE = 'DE';
    case DK = 'DK';
    case ES = 'ES';
    case FI = 'FI';
    case FR = 'FR';
    case GB = 'GB';
    case HU = 'HU';
    case IE = 'IE';
    case IN = 'IN';
    case IT = 'IT';
    case JP = 'JP';
    case LU = 'LU';
    case MX = 'MX';
    case NL = 'NL';
    case NO = 'NO';
    case NZ = 'NZ';
    case PL = 'PL';
    case PT = 'PT';
    case RO = 'RO';
    case SE = 'SE';
    case SG = 'SG';
    case TR = 'TR';
    case US = 'US';
    case ZA = 'ZA';

    public function label(): string
    {
        return match ($this) {
            self::AT => 'Austria',
            self::AU => 'Australia',
            self::BE => 'Belgium',
            self::CA => 'Canada',
            self::CH => 'Switzerland',
            self::CZ => 'Czech Republic',
            self::DE => 'Germany',
            self::DK => 'Denmark',
            self::ES => 'Spain',
            self::FI => 'Finland',
            self::FR => 'France',
            self::GB => 'United Kingdom',
            self::HU => 'Hungary',
            self::IE => 'Ireland',
            self::IN => 'India',
            self::IT => 'Italy',
            self::JP => 'Japan',
            self::LU => 'Luxembourg',
            self::MX => 'Mexico',
            self::NL => 'Netherlands',
            self::NO => 'Norway',
            self::NZ => 'New Zealand',
            self::PL => 'Poland',
            self::PT => 'Portugal',
            self::RO => 'Romania',
            self::SE => 'Sweden',
            self::SG => 'Singapore',
            self::TR => 'Turkey',
            self::US => 'United States',
            self::ZA => 'South Africa',
        };
    }
}
