<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Exceptions\Finance\PeriodFiledException;
use App\Models\Finance\Bill;
use App\Models\Finance\Invoice;
use App\Models\Finance\TaxPeriod;
use Illuminate\Support\Facades\DB;

class TaxService
{
    /**
     * Output tax (collected on invoices) vs input tax (paid on bills) for a
     * calendar window derived from the period key (YYYY-MM or YYYY-Qn).
     *
     * @return array{output_cents: int, input_cents: int, net_payable_cents: int}
     */
    public function periodSummary(string $period): array
    {
        [$from, $to] = $this->window($period);

        $output = (int) Invoice::query()
            ->whereNotIn('status', ['draft', 'voided'])
            ->whereBetween('issue_date', [$from, $to])
            ->sum('tax_total_cents');

        // v1: bills carry no tax split yet — input tax from bill meta lands
        // when invoicing-side tax fields ship on bills. Placeholder = 0 sum.
        $input = (int) Bill::query()
            ->whereBetween('bill_date', [$from, $to])
            ->where('amount_cents', '<', 0) // never matches — explicit v1 stub
            ->sum('amount_cents');

        return [
            'output_cents' => $output,
            'input_cents' => $input,
            'net_payable_cents' => $output - $input,
        ];
    }

    /** Snapshots the summary and locks the period. */
    public function filePeriod(string $period): TaxPeriod
    {
        return DB::transaction(function () use ($period): TaxPeriod {
            $existing = TaxPeriod::query()->where('period', $period)->first();

            if ($existing !== null && $existing->status === 'filed') {
                throw new PeriodFiledException;
            }

            $summary = $this->periodSummary($period);

            return TaxPeriod::query()->updateOrCreate(
                ['period' => $period],
                [
                    'output_tax_cents' => $summary['output_cents'],
                    'input_tax_cents' => $summary['input_cents'],
                    'net_payable_cents' => $summary['net_payable_cents'],
                    'status' => 'filed',
                ],
            );
        });
    }

    /** @return array{0: string, 1: string} from/to dates */
    private function window(string $period): array
    {
        if (str_contains($period, '-Q')) {
            [$year, $q] = explode('-Q', $period);
            $startMonth = ((int) $q - 1) * 3 + 1;
            $from = sprintf('%s-%02d-01', $year, $startMonth);

            return [$from, date('Y-m-t', strtotime(sprintf('%s-%02d-01', $year, $startMonth + 2)))];
        }

        return ["{$period}-01", date('Y-m-t', strtotime("{$period}-01"))];
    }
}
