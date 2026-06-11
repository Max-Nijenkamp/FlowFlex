<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\Activity;
use App\Models\CRM\Deal;
use App\Models\CRM\DealHealth;
use Illuminate\Support\Collection;
use Throwable;

class DealHealthService
{
    /** Factor weights *(assumed defaults 30/30/20/20)*. */
    private const array WEIGHTS = [
        'activity_recency' => 30,
        'stage_velocity' => 30,
        'engagement' => 20,
        'deal_age' => 20,
    ];

    private const int STALL_DAYS = 14; // *(assumed)*

    /**
     * Scores every open deal; per-deal try/catch; idempotent upsert.
     *
     * @return array{scored: int, failed: int}
     */
    public function recalculate(): array
    {
        $result = ['scored' => 0, 'failed' => 0];

        foreach (Deal::query()->where('status', 'open')->get() as $deal) {
            try {
                $factors = $this->factors($deal);
                $score = (int) round(collect($factors)->sum(fn (array $f) => $f['score'] * $f['weight'] / 100));

                DealHealth::query()->updateOrCreate(
                    ['deal_id' => $deal->id],
                    ['score' => $score, 'factors' => $factors, 'calculated_at' => now()],
                );
                $result['scored']++;
            } catch (Throwable $e) {
                report($e);
                $result['failed']++;
            }
        }

        return $result;
    }

    /** @return Collection<int, DealHealth> */
    public function atRisk(int $threshold = 40): Collection
    {
        return DealHealth::query()
            ->where('score', '<', $threshold)
            ->orderBy('score')
            ->get();
    }

    /** @return array<int, array{factor: string, score: int, weight: int, detail: string}> */
    private function factors(Deal $deal): array
    {
        $lastActivity = Activity::query()
            ->where('deal_id', $deal->id)
            ->latest('created_at')
            ->value('created_at');

        $daysSinceActivity = $lastActivity !== null
            ? (int) now()->diffInDays($lastActivity, true)
            : 999;

        $daysInStage = (int) now()->diffInDays($deal->stage_entered_at ?? $deal->created_at, true);
        $dealAge = (int) now()->diffInDays($deal->created_at, true);

        $activityCount = Activity::query()->where('deal_id', $deal->id)->count();

        return [
            [
                'factor' => 'activity_recency',
                'score' => $daysSinceActivity <= 3 ? 100 : ($daysSinceActivity <= 7 ? 70 : ($daysSinceActivity <= self::STALL_DAYS ? 40 : 0)),
                'weight' => self::WEIGHTS['activity_recency'],
                'detail' => "{$daysSinceActivity}d since last activity",
            ],
            [
                'factor' => 'stage_velocity',
                'score' => $daysInStage <= 14 ? 100 : ($daysInStage <= 30 ? 60 : 20),
                'weight' => self::WEIGHTS['stage_velocity'],
                'detail' => "{$daysInStage}d in current stage",
            ],
            [
                'factor' => 'engagement',
                'score' => min($activityCount * 20, 100),
                'weight' => self::WEIGHTS['engagement'],
                'detail' => "{$activityCount} activities logged",
            ],
            [
                'factor' => 'deal_age',
                'score' => $dealAge <= 30 ? 100 : ($dealAge <= 90 ? 60 : 20),
                'weight' => self::WEIGHTS['deal_age'],
                'detail' => "{$dealAge}d old",
            ],
        ];
    }
}
