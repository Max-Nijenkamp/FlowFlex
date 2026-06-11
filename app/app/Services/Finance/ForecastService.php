<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\Forecast;
use App\Models\Finance\JournalLine;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ForecastService
{
    /** @param array<array{key: string, description: string, value: string}>|null $assumptions */
    public function create(string $name, int $fiscalYear, string $scenario = 'base', ?array $assumptions = null): Forecast
    {
        return Forecast::create([
            'name' => $name,
            'fiscal_year' => $fiscalYear,
            'scenario' => $scenario,
            'assumptions' => $assumptions,
        ])->refresh();
    }

    /** Copies trailing-12-month actuals × (1 + growth) into the forecast year, per account/month. */
    public function seedFromActuals(string $forecastId, float $growthPercent): void
    {
        DB::transaction(function () use ($forecastId, $growthPercent): void {
            $forecast = Forecast::query()->findOrFail($forecastId);
            $factor = 1 + $growthPercent / 100;

            foreach (Account::query()->whereIn('type', ['revenue', 'expense'])->get() as $account) {
                for ($month = 1; $month <= 12; $month++) {
                    $actual = (int) JournalLine::query()
                        ->where('account_id', $account->id)
                        ->whereHas('entry', fn ($q) => $q
                            ->whereYear('entry_date', $forecast->fiscal_year - 1)
                            ->whereMonth('entry_date', $month))
                        ->selectRaw('COALESCE(SUM(ABS(debit_cents - credit_cents)), 0) as net')
                        ->value('net');

                    if ($actual === 0) {
                        continue;
                    }

                    $forecast->lines()->create([
                        'company_id' => $forecast->company_id,
                        'account_id' => $account->id,
                        'period' => sprintf('%d-%02d', $forecast->fiscal_year, $month),
                        'projected_cents' => (int) round($actual * $factor),
                    ]);
                }
            }
        });
    }

    /**
     * Forecast vs actual per line for closed periods.
     *
     * @return array<int, array{period: string, projected_cents: int, actual_cents: int}>
     */
    public function comparison(string $forecastId): array
    {
        $forecast = Forecast::query()->with('lines')->findOrFail($forecastId);
        $rows = [];

        foreach ($forecast->lines as $line) {
            [$year, $month] = explode('-', $line->period);
            $actual = (int) JournalLine::query()
                ->where('account_id', $line->account_id)
                ->whereHas('entry', fn ($q) => $q
                    ->whereYear('entry_date', (int) $year)
                    ->whereMonth('entry_date', (int) $month))
                ->selectRaw('COALESCE(SUM(ABS(debit_cents - credit_cents)), 0) as net')
                ->value('net');

            $rows[] = [
                'period' => $line->period,
                'projected_cents' => $line->projected_cents,
                'actual_cents' => $actual,
            ];
        }

        return $rows;
    }

    /** MAPE over periods that already have actuals; 0.0 when nothing closed yet. */
    public function accuracy(string $forecastId): float
    {
        $closed = collect($this->comparison($forecastId))
            ->filter(fn (array $row) => $row['actual_cents'] > 0
                && CarbonImmutable::parse($row['period'].'-01')->endOfMonth()->isPast());

        if ($closed->isEmpty()) {
            return 0.0;
        }

        $mape = $closed->avg(fn (array $row) => abs($row['actual_cents'] - $row['projected_cents']) / $row['actual_cents']);

        return round((float) $mape * 100, 1);
    }
}
