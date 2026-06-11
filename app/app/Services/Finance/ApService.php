<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\ApServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Exceptions\Finance\BillLinesMismatchException;
use App\Models\Finance\Bill;
use App\Models\Finance\PaymentRun;
use App\States\Finance\Bill\Approved;
use App\States\Finance\Bill\Paid;
use App\States\Finance\Bill\Scheduled;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApService implements ApServiceInterface
{
    public function __construct(private readonly LedgerServiceInterface $ledger) {}

    /**
     * @param  array<array{description: string, account_code: string, amount_cents: int}>  $lines
     */
    public function createBill(
        string $supplierId,
        string $billNumber,
        string $billDate,
        string $dueDate,
        array $lines,
        ?float $earlyDiscountPercent = null,
        ?string $earlyDiscountUntil = null,
    ): Bill {
        $total = array_sum(array_column($lines, 'amount_cents'));

        if ($total <= 0) {
            throw new BillLinesMismatchException('Bill total must be positive.');
        }

        return DB::transaction(function () use ($supplierId, $billNumber, $billDate, $dueDate, $lines, $total, $earlyDiscountPercent, $earlyDiscountUntil): Bill {
            $bill = Bill::create([
                'supplier_id' => $supplierId,
                'bill_number' => $billNumber,
                'amount_cents' => $total,
                'bill_date' => $billDate,
                'due_date' => $dueDate,
                'early_discount_percent' => $earlyDiscountPercent,
                'early_discount_until' => $earlyDiscountUntil,
            ])->refresh();

            foreach ($lines as $line) {
                $bill->lines()->create([
                    'company_id' => $bill->company_id,
                    'description' => $line['description'],
                    'account_id' => $this->ledger->accountByCode($line['account_code'])->id,
                    'amount_cents' => $line['amount_cents'],
                ]);
            }

            return $bill;
        });
    }

    /**
     * Approval posts the liability: Dr expense accounts / Cr AP. Approval
     * threshold routing (approve-large) enforced at the UI permission layer.
     */
    public function approveBill(string $billId): Bill
    {
        return DB::transaction(function () use ($billId): Bill {
            $bill = Bill::query()->with('lines')->lockForUpdate()->findOrFail($billId);

            $linesSum = (int) $bill->lines->sum('amount_cents');
            if ($linesSum !== $bill->amount_cents) {
                throw new BillLinesMismatchException;
            }

            $glLines = $bill->lines
                ->map(fn ($line) => [
                    'account_code' => $line->account->code ?? '6100',
                    'debit_cents' => $line->amount_cents,
                ])
                ->all();
            $glLines[] = ['account_code' => '2000', 'credit_cents' => $bill->amount_cents];

            $this->ledger->post(
                reference: "BILL-{$bill->bill_number}",
                description: "Bill {$bill->bill_number} approved",
                entryDate: now()->toDateString(),
                lines: $glLines,
                sourceType: 'bill',
                sourceId: $bill->id,
            );

            $bill->status->transitionTo(Approved::class);
            $bill->update(['approved_by' => Auth::guard('web')->id()]);

            return $bill->refresh();
        });
    }

    /** @param array<string> $billIds approved bills to schedule */
    public function createPaymentRun(string $runDate, array $billIds): PaymentRun
    {
        return DB::transaction(function () use ($runDate, $billIds): PaymentRun {
            $bills = Bill::query()->whereIn('id', $billIds)->whereState('status', Approved::class)->get();

            $run = PaymentRun::create([
                'run_date' => $runDate,
                'total_cents' => (int) $bills->sum('amount_cents'),
            ]);

            foreach ($bills as $bill) {
                $bill->status->transitionTo(Scheduled::class);
                $bill->update(['payment_run_id' => $run->id]);
            }

            return $run->refresh();
        });
    }

    /** Pays every bill in the run atomically: early discounts applied in window, GL cash entries posted. */
    public function executeRun(string $runId): PaymentRun
    {
        return DB::transaction(function () use ($runId): PaymentRun {
            $run = PaymentRun::query()->with('bills')->lockForUpdate()->findOrFail($runId);
            $today = CarbonImmutable::today();
            $total = 0;

            foreach ($run->bills as $bill) {
                $payable = Money::ofMinor($bill->amount_cents, $bill->currency);

                $inWindow = $bill->early_discount_percent !== null
                    && $bill->early_discount_until !== null
                    && ! $today->isAfter(CarbonImmutable::parse($bill->early_discount_until->toDateString()));

                if ($inWindow) {
                    $discount = $payable->multipliedBy(
                        (string) ($bill->early_discount_percent / 100),
                        RoundingMode::HALF_UP,
                    );
                    $payable = $payable->minus($discount);
                }

                $payableCents = (int) $payable->getMinorAmount()->toInt();
                $discountCents = $bill->amount_cents - $payableCents;

                $glLines = [
                    ['account_code' => '2000', 'debit_cents' => $bill->amount_cents],
                    ['account_code' => '1000', 'credit_cents' => $payableCents],
                ];
                if ($discountCents > 0) {
                    $glLines[] = ['account_code' => '7000', 'credit_cents' => $discountCents]; // discount = gain
                }

                $this->ledger->post(
                    reference: "PAY-{$bill->bill_number}",
                    description: "Bill {$bill->bill_number} paid in run {$run->id}",
                    entryDate: $today->toDateString(),
                    lines: $glLines,
                    sourceType: 'payment-run',
                    sourceId: $run->id,
                );

                $bill->status->transitionTo(Paid::class);
                $bill->update(['paid_at' => now()]);
                $total += $payableCents;
            }

            $run->update(['status' => 'executed', 'total_cents' => $total]);

            return $run->refresh();
        });
    }

    /** @return array<string, int> open AP per aging bucket */
    public function aging(): array
    {
        $today = CarbonImmutable::today();
        $buckets = ['current' => 0, '1-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];

        Bill::query()
            ->whereNotState('status', Paid::class)
            ->whereState('status', [Approved::class, Scheduled::class])
            ->get()
            ->each(function (Bill $bill) use (&$buckets, $today): void {
                $overdue = $bill->due_date->isAfter($today) ? 0 : (int) $bill->due_date->diffInDays($today);
                $bucket = match (true) {
                    $overdue <= 0 => 'current',
                    $overdue <= 30 => '1-30',
                    $overdue <= 60 => '31-60',
                    $overdue <= 90 => '61-90',
                    default => '90+',
                };
                $buckets[$bucket] += $bill->amount_cents;
            });

        return $buckets;
    }
}
