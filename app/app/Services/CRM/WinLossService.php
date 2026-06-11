<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\WinLoss;
use Carbon\CarbonImmutable;

class WinLossService
{
    /** Called from the deal close path (same-domain direct call allowed). */
    public function record(string $dealId, string $outcome, string $reason, ?string $competitor = null): WinLoss
    {
        return WinLoss::query()->updateOrCreate(
            ['deal_id' => $dealId],
            ['outcome' => $outcome, 'reason' => $reason, 'competitor' => $competitor],
        );
    }

    /**
     * @return array{won: int, lost: int, win_rate: float, top_loss_reasons: array<string, int>}
     */
    public function analysis(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = WinLoss::query()
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $won = $rows->where('outcome', 'won')->count();
        $lost = $rows->where('outcome', 'lost')->count();

        return [
            'won' => $won,
            'lost' => $lost,
            'win_rate' => ($won + $lost) > 0 ? round($won / ($won + $lost) * 100, 1) : 0.0,
            'top_loss_reasons' => $rows->where('outcome', 'lost')
                ->groupBy('reason')
                ->map(fn ($group) => $group->count())
                ->sortDesc()
                ->take(5)
                ->all(),
        ];
    }
}
