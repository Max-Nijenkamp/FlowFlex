<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Exceptions\Finance\MissingExchangeRateException;
use App\Models\Finance\ExchangeRate;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;

class CurrencyService
{
    public const string BASE = 'EUR';

    /** Most recent rate at or before the date. */
    public function rateFor(string $from, string $to, CarbonImmutable $date): BigDecimal
    {
        if ($from === $to) {
            return BigDecimal::one();
        }

        $rate = ExchangeRate::query()
            ->where('from_currency', $from)
            ->where('to_currency', $to)
            ->whereDate('effective_date', '<=', $date->toDateString())
            ->orderByDesc('effective_date')
            ->first();

        if ($rate === null) {
            throw new MissingExchangeRateException("No {$from}→{$to} rate on or before {$date->toDateString()}.");
        }

        return BigDecimal::of((string) $rate->rate);
    }

    /** Converts to base currency at the transaction-date rate — GL only ever stores base. */
    public function toBase(Money $foreign, CarbonImmutable $date): Money
    {
        $code = $foreign->getCurrency()->getCurrencyCode();

        if ($code === self::BASE) {
            return $foreign;
        }

        $rate = $this->rateFor($code, self::BASE, $date);

        return Money::of(
            $foreign->getAmount()->multipliedBy($rate),
            self::BASE,
            roundingMode: RoundingMode::HALF_UP,
        );
    }

    /** Realised FX on payment: difference between invoice-date and payment-date base values. */
    public function realisedFxCents(Money $foreign, CarbonImmutable $invoiceDate, CarbonImmutable $paymentDate): int
    {
        $atInvoice = $this->toBase($foreign, $invoiceDate);
        $atPayment = $this->toBase($foreign, $paymentDate);

        return (int) $atPayment->minus($atInvoice)->getMinorAmount()->toInt();
    }
}
