<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\Deal;
use App\Models\CRM\ForecastSnapshot;
use App\Models\CRM\Quota;
use Illuminate\Validation\ValidationException;

class SalesForecastService
{
    public const array CATEGORIES = ['commit', 'best-case', 'pipeline', 'closed'];

    /**
     * Live forecast per category + quota attainment. Roll-up across all reps
     * when ownerId is null.
     *
     * @return array{categories: array<string, int>, weighted_cents: int, quota_cents: int, attainment: float, coverage: float}
     */
    public function forecast(string $period, ?string $ownerId = null): array
    {
        $open = Deal::query()
            ->where('status', 'open')
            ->when($ownerId !== null, fn ($q) => $q->where('owner_id', $ownerId))
            ->get();

        $closedWon = (int) Deal::query()
            ->where('status', 'won')
            ->when($ownerId !== null, fn ($q) => $q->where('owner_id', $ownerId))
            ->sum('value_cents');

        $categories = array_fill_keys(self::CATEGORIES, 0);
        $categories['closed'] = $closedWon;
        $weighted = 0;

        foreach ($open as $deal) {
            $categories[$deal->forecast_category ?? 'pipeline'] += $deal->value_cents;
            $weighted += (int) round($deal->value_cents * $deal->probability / 100);
        }

        $quota = (int) Quota::query()
            ->where('period', $period)
            ->when($ownerId !== null, fn ($q) => $q->where('owner_id', $ownerId))
            ->sum('quota_cents');

        return [
            'categories' => $categories,
            'weighted_cents' => $weighted,
            'quota_cents' => $quota,
            'attainment' => $quota > 0 ? round($closedWon / $quota * 100, 1) : 0.0,
            'coverage' => $quota > 0 ? round(($categories['pipeline'] + $categories['best-case'] + $categories['commit']) / $quota, 2) : 0.0,
        ];
    }

    /** Reps tag open deals only. */
    public function setCategory(string $dealId, string $category): Deal
    {
        if (! in_array($category, self::CATEGORIES, true)) {
            throw ValidationException::withMessages(['category' => 'Unknown forecast category.']);
        }

        $deal = Deal::query()->findOrFail($dealId);

        if ((string) $deal->status !== 'open') {
            throw ValidationException::withMessages(['deal' => 'Forecast category is settable on open deals only.']);
        }

        $deal->update(['forecast_category' => $category]);

        return $deal->refresh();
    }

    /** Weekly snapshot per owner/category — idempotent per (owner, period, week). */
    public function captureSnapshots(string $period): int
    {
        $captured = 0;
        $weekKey = now()->startOfWeek();

        $owners = Deal::query()->where('status', 'open')->distinct()->pluck('owner_id')->filter();

        foreach ($owners as $ownerId) {
            $exists = ForecastSnapshot::query()
                ->where('owner_id', $ownerId)
                ->where('period', $period)
                ->where('captured_at', '>=', $weekKey)
                ->exists();

            if ($exists) {
                continue;
            }

            $forecast = $this->forecast($period, $ownerId);

            foreach ($forecast['categories'] as $category => $cents) {
                ForecastSnapshot::create([
                    'owner_id' => $ownerId,
                    'period' => $period,
                    'category' => $category,
                    'amount_cents' => $cents,
                    'captured_at' => now(),
                ]);
            }
            $captured++;
        }

        return $captured;
    }
}
