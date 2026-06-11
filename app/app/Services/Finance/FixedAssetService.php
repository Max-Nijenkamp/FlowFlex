<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Exceptions\Finance\AlreadyDisposedException;
use App\Models\Finance\FixedAsset;
use Illuminate\Support\Facades\DB;
use Throwable;

class FixedAssetService
{
    public function __construct(private readonly LedgerServiceInterface $ledger) {}

    public function create(
        string $name,
        string $category,
        int $costCents,
        string $purchaseDate,
        int $usefulLifeMonths,
        string $method = 'straight-line',
        int $salvageCents = 0,
    ): FixedAsset {
        $asset = FixedAsset::create([
            'name' => $name,
            'category' => $category,
            'cost_cents' => $costCents,
            'purchase_date' => $purchaseDate,
            'useful_life_months' => $usefulLifeMonths,
            'method' => $method,
            'salvage_cents' => $salvageCents,
        ])->refresh();

        // Capitalise: Dr Fixed Assets / Cr Cash.
        $this->ledger->post(
            reference: "FA-{$asset->id}",
            description: "Asset acquired: {$name}",
            entryDate: $purchaseDate,
            lines: [
                ['account_code' => '1200', 'debit_cents' => $costCents],
                ['account_code' => '1000', 'credit_cents' => $costCents],
            ],
            sourceType: 'fixed-asset',
            sourceId: $asset->id,
        );

        return $asset;
    }

    /**
     * Full-life schedule. Straight-line: equal monthly slices, final month
     * absorbs the rounding remainder so the sum is exactly cost − salvage.
     * Declining balance: 2/life rate, floored at salvage.
     *
     * @return array<int, array{month: int, depreciation_cents: int}>
     */
    public function schedule(string $assetId): array
    {
        $asset = FixedAsset::query()->findOrFail($assetId);
        $depreciable = $asset->cost_cents - $asset->salvage_cents;
        $rows = [];

        if ($asset->method === 'straight-line') {
            $monthly = intdiv($depreciable, $asset->useful_life_months);
            $allocated = 0;
            for ($m = 1; $m <= $asset->useful_life_months; $m++) {
                $slice = $m === $asset->useful_life_months ? $depreciable - $allocated : $monthly;
                $rows[] = ['month' => $m, 'depreciation_cents' => $slice];
                $allocated += $slice;
            }

            return $rows;
        }

        // Declining balance: double-declining monthly rate, never below salvage.
        $nbv = $asset->cost_cents;
        $rate = 2.0 / $asset->useful_life_months;
        for ($m = 1; $m <= $asset->useful_life_months; $m++) {
            $slice = (int) round($nbv * $rate);
            if ($nbv - $slice < $asset->salvage_cents) {
                $slice = $nbv - $asset->salvage_cents;
            }
            $rows[] = ['month' => $m, 'depreciation_cents' => max($slice, 0)];
            $nbv -= $slice;
        }

        return $rows;
    }

    /**
     * Monthly batch: idempotent per (asset, period); per-asset failure
     * doesn't stop the batch.
     *
     * @return array{processed: int, skipped: int, failed: int}
     */
    public function runMonthlyDepreciation(string $period): array
    {
        $result = ['processed' => 0, 'skipped' => 0, 'failed' => 0];

        foreach (FixedAsset::query()->where('status', 'active')->get() as $asset) {
            if ($asset->depreciationEntries()->where('period', $period)->exists()) {
                $result['skipped']++;

                continue;
            }

            try {
                DB::transaction(function () use ($asset, $period): void {
                    $monthsDone = $asset->depreciationEntries()->count();
                    $slice = $this->schedule($asset->id)[$monthsDone]['depreciation_cents'] ?? 0;

                    if ($slice <= 0) {
                        return;
                    }

                    $entry = $this->ledger->post(
                        reference: "DEP-{$asset->id}-{$period}",
                        description: "Depreciation {$asset->name} {$period}",
                        entryDate: now()->toDateString(),
                        lines: [
                            ['account_code' => '6200', 'debit_cents' => $slice],
                            ['account_code' => '1250', 'credit_cents' => $slice],
                        ],
                        sourceType: 'depreciation',
                        sourceId: $asset->id,
                    );

                    $asset->depreciationEntries()->create([
                        'company_id' => $asset->company_id,
                        'period' => $period,
                        'depreciation_cents' => $slice,
                        'journal_entry_id' => $entry->id,
                    ]);

                    $accumulated = $asset->accumulated_depreciation_cents + $slice;
                    $asset->update([
                        'accumulated_depreciation_cents' => $accumulated,
                        'status' => $asset->cost_cents - $accumulated <= $asset->salvage_cents
                            ? 'fully-depreciated'
                            : 'active',
                    ]);
                });
                $result['processed']++;
            } catch (Throwable $e) {
                report($e);
                $result['failed']++;
            }
        }

        return $result;
    }

    /** Gain/loss = proceeds − NBV → GL; double disposal rejected. */
    public function dispose(string $assetId, int $proceedsCents): FixedAsset
    {
        return DB::transaction(function () use ($assetId, $proceedsCents): FixedAsset {
            $asset = FixedAsset::query()->lockForUpdate()->findOrFail($assetId);

            if ($asset->disposed_at !== null) {
                throw new AlreadyDisposedException;
            }

            $nbv = $asset->netBookValueCents();
            $gain = $proceedsCents - $nbv;

            $lines = [
                ['account_code' => '1000', 'debit_cents' => $proceedsCents],
                ['account_code' => '1250', 'debit_cents' => $asset->accumulated_depreciation_cents],
                ['account_code' => '1200', 'credit_cents' => $asset->cost_cents],
            ];
            if ($gain > 0) {
                $lines[] = ['account_code' => '8000', 'credit_cents' => $gain];
            } elseif ($gain < 0) {
                $lines[] = ['account_code' => '8000', 'debit_cents' => -$gain];
            }

            $this->ledger->post(
                reference: "DISP-{$asset->id}",
                description: "Asset disposed: {$asset->name}",
                entryDate: now()->toDateString(),
                lines: $lines,
                sourceType: 'asset-disposal',
                sourceId: $asset->id,
            );

            $asset->update([
                'status' => 'disposed',
                'disposed_at' => now(),
                'disposal_proceeds_cents' => $proceedsCents,
            ]);

            return $asset->refresh();
        });
    }
}
