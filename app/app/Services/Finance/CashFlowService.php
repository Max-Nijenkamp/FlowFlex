<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\BankAccount;
use App\Models\Finance\Bill;
use App\Models\Finance\CashflowItem;
use App\Models\Finance\CashflowProjection;
use App\Models\Finance\Invoice;
use App\States\Finance\Bill\Approved;
use App\States\Finance\Bill\Scheduled;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CashFlowService
{
    public const int WEEKS = 13;

    /**
     * Regenerates the 13-week projection: inflows from open invoice due
     * dates, outflows from approved/scheduled bills; opening from bank
     * balances; manual items preserved.
     */
    public function rebuild(): void
    {
        DB::transaction(function (): void {
            $start = CarbonImmutable::today()->startOfWeek();

            // Preserve manual items, drop computed ones.
            $manual = CashflowItem::query()->where('source', 'manual')->get();
            CashflowProjection::query()->where('is_actual', false)->delete();

            $opening = (int) BankAccount::query()->sum('current_balance_cents');

            for ($week = 0; $week < self::WEEKS; $week++) {
                $weekStart = $start->addWeeks($week);
                $weekEnd = $weekStart->addDays(6);

                $projection = CashflowProjection::create([
                    'week_start' => $weekStart->toDateString(),
                    'opening_cents' => $opening,
                ])->refresh();

                $inflow = 0;
                $outflow = 0;

                foreach ($this->openInvoicesDue($weekStart, $weekEnd) as $invoice) {
                    $amount = $invoice->total_cents - $invoice->paid_amount_cents;
                    $projection->items()->create([
                        'company_id' => $projection->company_id,
                        'type' => 'inflow', 'source' => 'invoice', 'source_id' => $invoice->id,
                        'description' => "Invoice {$invoice->invoice_number}",
                        'amount_cents' => $amount,
                        'expected_date' => $invoice->due_date->toDateString(),
                    ]);
                    $inflow += $amount;
                }

                foreach ($this->openBillsDue($weekStart, $weekEnd) as $bill) {
                    $projection->items()->create([
                        'company_id' => $projection->company_id,
                        'type' => 'outflow', 'source' => 'bill', 'source_id' => $bill->id,
                        'description' => "Bill {$bill->bill_number}",
                        'amount_cents' => $bill->amount_cents,
                        'expected_date' => $bill->due_date->toDateString(),
                    ]);
                    $outflow += $bill->amount_cents;
                }

                foreach ($manual as $item) {
                    $date = CarbonImmutable::parse($item->expected_date->toDateString());
                    if ($date->between($weekStart, $weekEnd)) {
                        $projection->items()->create([
                            'company_id' => $projection->company_id,
                            'type' => $item->type, 'source' => 'manual',
                            'description' => $item->description,
                            'amount_cents' => $item->amount_cents,
                            'expected_date' => $item->expected_date->toDateString(),
                        ]);
                        if ($item->type === 'inflow') {
                            $inflow += $item->amount_cents;
                        } else {
                            $outflow += $item->amount_cents;
                        }
                    }
                }

                $closing = $opening + $inflow - $outflow;
                $projection->update([
                    'inflow_cents' => $inflow,
                    'outflow_cents' => $outflow,
                    'closing_cents' => $closing,
                ]);
                $opening = $closing;
            }
        });
    }

    /** @return Collection<int, CashflowProjection> */
    public function projection(): Collection
    {
        return CashflowProjection::query()
            ->where('is_actual', false)
            ->orderBy('week_start')
            ->get();
    }

    public function addManualItem(string $type, string $description, int $amountCents, string $expectedDate): void
    {
        $week = CarbonImmutable::parse($expectedDate)->startOfWeek();
        $projection = CashflowProjection::query()
            ->where('week_start', $week->toDateString())
            ->where('is_actual', false)
            ->first();

        if ($projection === null) {
            $projection = CashflowProjection::create(['week_start' => $week->toDateString()])->refresh();
        }

        $projection->items()->create([
            'company_id' => $projection->company_id,
            'type' => $type, 'source' => 'manual',
            'description' => $description,
            'amount_cents' => $amountCents,
            'expected_date' => $expectedDate,
        ]);

        $this->rebuild();
    }

    /** @return Collection<int, Invoice> */
    private function openInvoicesDue(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return Invoice::query()
            ->whereNotIn('status', ['draft', 'paid', 'voided'])
            ->whereDate('due_date', '>=', $from->toDateString())
            ->whereDate('due_date', '<=', $to->toDateString())
            ->get()
            ->filter(fn (Invoice $i) => $i->total_cents - $i->paid_amount_cents > 0)
            ->values();
    }

    /** @return Collection<int, Bill> */
    private function openBillsDue(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return Bill::query()
            ->whereState('status', [Approved::class, Scheduled::class])
            ->whereDate('due_date', '>=', $from->toDateString())
            ->whereDate('due_date', '<=', $to->toDateString())
            ->get();
    }
}
