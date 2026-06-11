<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Settings\CompanyLocaleSettings;
use Brick\Money\Money;
use Illuminate\Support\Carbon;

/**
 * Single formatting API for dates, numbers, and money, driven by
 * CompanyLocaleSettings. DB stores UTC + minor units; display converts here.
 */
class LocaleFormatter
{
    public function __construct(
        private readonly CompanyLocaleSettings $settings,
    ) {}

    public function date(Carbon|\DateTimeInterface|string $date): string
    {
        return Carbon::parse($date)
            ->setTimezone($this->settings->timezone)
            ->format($this->settings->date_format);
    }

    public function dateTime(Carbon|\DateTimeInterface|string $date): string
    {
        return Carbon::parse($date)
            ->setTimezone($this->settings->timezone)
            ->format($this->settings->date_format.' H:i');
    }

    public function number(float|int $value): string
    {
        [$decimal, $thousands] = $this->separators();

        return number_format((float) $value, $this->settings->decimal_places, $decimal, $thousands);
    }

    public function money(int $minorUnits, ?string $currency = null): string
    {
        $currency ??= $this->settings->currency;
        $money = Money::ofMinor($minorUnits, $currency);
        $amount = $this->number($money->getAmount()->toFloat());
        $symbol = match ($currency) {
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            default => $currency,
        };

        return $this->settings->currency_position === 'after'
            ? "{$amount} {$symbol}"
            : "{$symbol}{$amount}";
    }

    /** @return array{string, string} [decimal, thousands] */
    private function separators(): array
    {
        // nl/de convention: comma decimals; en: dot decimals.
        return in_array($this->settings->locale, ['nl', 'de'], true)
            ? [',', '.']
            : ['.', ','];
    }
}
