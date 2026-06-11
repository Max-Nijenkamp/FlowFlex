<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\DeiAttribute;
use App\Models\HR\DeiSnapshot;

class DeiSnapshotService
{
    /** Groups smaller than this are suppressed before storage. */
    public const int SUPPRESSION_THRESHOLD = 5;

    /**
     * Decrypts the attribute set, aggregates per dimension, suppresses small
     * groups, stores the snapshot, discards individuals. Dashboards read
     * snapshots ONLY — never live decrypt-and-group.
     */
    public function generate(string $period): void
    {
        DeiAttribute::query()
            ->get()
            ->groupBy('dimension')
            ->each(function ($attributes, string $dimension) use ($period): void {
                $breakdown = $attributes
                    ->groupBy(fn (DeiAttribute $a) => $a->value) // decrypts
                    ->map(fn ($group) => $group->count())
                    ->filter(fn (int $count) => $count >= self::SUPPRESSION_THRESHOLD)
                    ->all();

                DeiSnapshot::query()->updateOrCreate(
                    ['period' => $period, 'dimension' => $dimension],
                    ['breakdown' => $breakdown],
                );
            });
    }
}
