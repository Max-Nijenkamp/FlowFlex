<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\Account;
use App\Models\CRM\PriceBook;
use App\Models\CRM\Product;
use App\Models\CRM\VolumeDiscount;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;

class PricingService
{
    private const int MARGIN_THRESHOLD_PERCENT = 10; // *(assumed)* min margin above cost

    /**
     * CPQ resolution: account book → default book → standard price, then
     * volume tier, then margin guard.
     *
     * @return array{unit_price_cents: int, source: string, discount_percent: float, below_margin: bool}
     */
    public function resolve(string $productId, ?string $accountId, float $quantity, ?CarbonImmutable $date = null): array
    {
        $date ??= CarbonImmutable::today();
        $product = Product::query()->findOrFail($productId);

        [$price, $source] = $this->basePrice($product, $accountId, $date);

        $tier = VolumeDiscount::query()
            ->where('product_id', $product->id)
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('min_quantity')
            ->first();

        $discount = $tier !== null ? $tier->discount_percent : 0.0;

        if ($discount > 0) {
            $money = Money::ofMinor($price, 'EUR');
            $price = (int) $money->minus(
                $money->multipliedBy((string) ($discount / 100), RoundingMode::HALF_UP),
            )->getMinorAmount()->toInt();
        }

        $floor = (int) round($product->cost_cents * (1 + self::MARGIN_THRESHOLD_PERCENT / 100));

        return [
            'unit_price_cents' => $price,
            'source' => $source,
            'discount_percent' => (float) $discount,
            'below_margin' => $price < $floor,
        ];
    }

    /** @return array{0: int, 1: string} */
    private function basePrice(Product $product, ?string $accountId, CarbonImmutable $date): array
    {
        $candidateBooks = [];

        if ($accountId !== null) {
            $account = Account::query()->find($accountId);
            if ($account?->price_book_id !== null) {
                $candidateBooks[] = [$account->price_book_id, 'account-book'];
            }
        }

        $default = PriceBook::query()->where('is_default', true)->first();
        if ($default !== null) {
            $candidateBooks[] = [$default->id, 'default-book'];
        }

        foreach ($candidateBooks as [$bookId, $source]) {
            $entry = PriceBook::query()->findOrFail($bookId)
                ->entries()
                ->where('product_id', $product->id)
                ->where(fn ($q) => $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', $date->toDateString()))
                ->where(fn ($q) => $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', $date->toDateString()))
                ->orderByDesc('valid_from')
                ->first();

            if ($entry !== null) {
                return [$entry->price_cents, $source];
            }
        }

        return [$product->standard_price_cents, 'standard'];
    }
}
