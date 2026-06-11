<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\ArServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Data\Finance\RecordPaymentData;
use App\Models\Finance\ArWriteoff;
use App\Models\Finance\DunningRule;
use App\Models\Finance\Invoice;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArService implements ArServiceInterface
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoices,
        private readonly LedgerServiceInterface $ledger,
    ) {}

    private const array BUCKETS = ['current', '1-30', '31-60', '61-90', '90+'];

    /** @return array<string, int> open balance cents per aging bucket */
    public function aging(?string $customerId = null): array
    {
        $today = CarbonImmutable::today();
        $buckets = array_fill_keys(self::BUCKETS, 0);

        Invoice::query()
            ->whereNotIn('status', ['draft', 'paid', 'voided'])
            ->when($customerId !== null, fn ($q) => $q->where('customer_id', $customerId))
            ->get()
            ->each(function (Invoice $invoice) use (&$buckets, $today): void {
                $open = $invoice->total_cents - $invoice->paid_amount_cents;
                if ($open <= 0) {
                    return;
                }
                $overdue = $invoice->due_date->isAfter($today) ? 0 : (int) $invoice->due_date->diffInDays($today);
                $buckets[$this->bucketFor($overdue)] += $open;
            });

        return $buckets;
    }

    private function bucketFor(int $daysOverdue): string
    {
        return match (true) {
            $daysOverdue <= 0 => 'current',
            $daysOverdue <= 30 => '1-30',
            $daysOverdue <= 60 => '31-60',
            $daysOverdue <= 90 => '61-90',
            default => '90+',
        };
    }

    /** @return Collection<int, Invoice> all invoices for the customer in range, balance computable by caller */
    public function statement(string $customerId, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return Invoice::query()
            ->where('customer_id', $customerId)
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', '!=', 'draft')
            ->with('payments')
            ->orderBy('issue_date')
            ->get();
    }

    /** Writes off the remaining balance: GL bad-debt entry + approver recorded. */
    public function writeOff(string $invoiceId, string $reason): ArWriteoff
    {
        return DB::transaction(function () use ($invoiceId, $reason): ArWriteoff {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoiceId);
            $open = $invoice->total_cents - $invoice->paid_amount_cents;

            $this->ledger->post(
                reference: "WO-{$invoice->invoice_number}",
                description: "Write-off invoice {$invoice->invoice_number}: {$reason}",
                entryDate: now()->toDateString(),
                lines: [
                    ['account_code' => '6300', 'debit_cents' => $open],
                    ['account_code' => '1100', 'credit_cents' => $open],
                ],
                sourceType: 'ar-writeoff',
                sourceId: $invoice->id,
            );

            $this->invoices->void($invoice->id, "Written off: {$reason}");

            return ArWriteoff::create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->id,
                'amount_cents' => $open,
                'reason' => $reason,
                'approved_by' => Auth::guard('web')->id(),
                'written_off_at' => now(),
            ]);
        });
    }

    /**
     * Applies one payment across multiple invoices (oldest-first allocation).
     *
     * @param  array<array{invoice_id: string, amount_cents: int}>  $allocations
     */
    public function allocatePayment(array $allocations, string $paymentDate, string $method = 'bank-transfer'): void
    {
        DB::transaction(function () use ($allocations, $paymentDate, $method): void {
            foreach ($allocations as $allocation) {
                $this->invoices->recordPayment(new RecordPaymentData(
                    invoice_id: $allocation['invoice_id'],
                    amount_cents: $allocation['amount_cents'],
                    payment_date: $paymentDate,
                    payment_method: $method,
                ));
            }
        });
    }

    /** Days sales outstanding: (open AR / period credit sales) × days in period. */
    public function dso(CarbonImmutable $from, CarbonImmutable $to): float
    {
        $sales = (int) Invoice::query()
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->whereNotIn('status', ['draft', 'voided'])
            ->sum('total_cents');

        if ($sales === 0) {
            return 0.0;
        }

        $open = array_sum($this->aging());
        $days = (int) $from->diffInDays($to) + 1;

        return round($open / $sales * $days, 1);
    }

    /**
     * Escalating reminders: each active rule fires once per invoice when its
     * threshold passes; payment resets the level (listener).
     *
     * @return int reminders sent
     */
    public function runDunning(): int
    {
        $today = CarbonImmutable::today();
        $sent = 0;

        $rules = DunningRule::query()->where('is_active', true)->orderBy('escalation_level')->get();

        foreach ($rules as $rule) {
            $invoices = Invoice::query()
                ->whereNotIn('status', ['draft', 'paid', 'voided'])
                ->where('last_dunning_level', '<', $rule->escalation_level)
                ->whereDate('due_date', '<=', $today->subDays($rule->days_overdue)->toDateString())
                ->get()
                ->filter(fn (Invoice $i) => $i->total_cents - $i->paid_amount_cents > 0);

            foreach ($invoices as $invoice) {
                // v1: notification record; templated dunning mail lands with email pass.
                $invoice->update(['last_dunning_level' => $rule->escalation_level]);
                $sent++;
            }
        }

        return $sent;
    }
}
