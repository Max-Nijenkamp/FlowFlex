<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\TaxRate;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

/** Single tax-math entry point — all consuming modules call this. */
class TaxCalculator
{
    public function forLine(int $amountCents, TaxRate $rate, string $currency = 'EUR'): Money
    {
        if ($rate->is_reverse_charge) {
            return Money::ofMinor(0, $currency); // intra-EU B2B: buyer self-assesses
        }

        return Money::ofMinor($amountCents, $currency)
            ->multipliedBy((string) ($rate->rate_basis_points / 10000), RoundingMode::HALF_UP);
    }
}
