<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Events\Finance\InvoicePaid;
use App\Exceptions\Finance\CannotVoidPaidInvoiceException;
use App\Mail\Finance\CustomerInvoiceMail;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceLine;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\Payment;
use App\Models\User;
use App\States\Finance\Invoice\Paid;
use App\States\Finance\Invoice\PartiallyPaid;
use App\States\Finance\Invoice\Sent;
use App\States\Finance\Invoice\Voided;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Owns fin_invoices/fin_invoice_lines/fin_payments/fin_customers writes
 * (finance.invoicing). Totals are brick/money with per-line tax rounding;
 * payments post AR journals through LedgerService — never raw ledger
 * writes; the invoice number is assigned gap-free at send time under lock.
 */
class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(private readonly LedgerServiceInterface $ledger) {}

    public function create(CreateInvoiceData $data): Invoice
    {
        if ($data->lines === []) {
            throw ValidationException::withMessages(['lines' => 'An invoice needs at least one line.']);
        }

        /** @var Customer $customer */
        $customer = Customer::query()->findOrFail($data->customerId);
        $companyId = app(CompanyContext::class)->current()->id;

        return DB::transaction(function () use ($data, $customer, $companyId): Invoice {
            $issue = $data->issueDate !== null ? now()->parse($data->issueDate) : now();
            $due = $data->dueDate !== null
                ? now()->parse($data->dueDate)
                : $issue->copy()->addDays($customer->payment_terms_days);

            /** @var Invoice $invoice */
            $invoice = Invoice::query()->create([
                'company_id' => $companyId,
                'customer_id' => $customer->id,
                'issue_date' => $issue->toDateString(),
                'due_date' => $due->toDateString(),
                'currency' => 'EUR',
                'discount_percent' => $data->discountPercent,
                'notes' => $data->notes,
                'recurring_schedule' => $data->recurringSchedule,
                'next_recurring_at' => $data->recurringSchedule !== null
                    ? self::nextRecurringDate($issue->toImmutable(), $data->recurringSchedule)
                    : null,
                'source_deal_id' => $data->sourceDealId,
            ]);

            foreach ($data->lines as $line) {
                $quantity = (string) $line['quantity'];
                $rate = (string) ($line['tax_rate_percent'] ?? 21);

                $net = Money::ofMinor($line['unit_price_cents'], 'EUR')
                    ->multipliedBy($quantity, RoundingMode::HalfUp);
                $tax = $net->multipliedBy((string) ((float) $rate / 100), RoundingMode::HalfUp);

                $invoice->lines()->create([
                    'company_id' => $companyId,
                    'description' => $line['description'],
                    'quantity' => $quantity,
                    'unit_price_cents' => $line['unit_price_cents'],
                    'tax_rate_percent' => $rate,
                    'tax_cents' => $tax->getMinorAmount()->toInt(),
                    'line_total_cents' => $net->plus($tax)->getMinorAmount()->toInt(),
                ]);
            }

            return $this->recalculateTotals($invoice);
        });
    }

    public function send(string $invoiceId): Invoice
    {
        return DB::transaction(function () use ($invoiceId): Invoice {
            /** @var Invoice $invoice */
            $invoice = Invoice::query()->whereKey($invoiceId)->lockForUpdate()->firstOrFail();

            if ($invoice->invoice_number === null) {
                $invoice->invoice_number = $this->nextInvoiceNumber($invoice->company_id);
            }

            $invoice->status->transitionTo(Sent::class);
            $invoice->save();

            // Revenue recognition at send: AR ↑ / revenue + VAT ↑. With a
            // discount the VAT share shrinks proportionally so the entry
            // still balances to the cent.
            if ($invoice->total_cents > 0) {
                $discountFactor = 1 - ((float) $invoice->discount_percent / 100);
                $taxPortion = (int) round($invoice->tax_total_cents * $discountFactor);

                $this->ledger->post(
                    reference: (string) $invoice->invoice_number,
                    description: "Invoice {$invoice->invoice_number}",
                    entryDate: now(),
                    lines: [
                        ['account_id' => LedgerService::accountIdByCode('1200'), 'debit_cents' => $invoice->total_cents],
                        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => $invoice->total_cents - $taxPortion],
                        ['account_id' => LedgerService::accountIdByCode('2200'), 'credit_cents' => $taxPortion],
                    ],
                    sourceType: 'invoice',
                    sourceId: $invoice->id,
                );
            }

            $customer = $invoice->customer()->first();
            if ($customer instanceof Customer) {
                Mail::to($customer->email)->queue(new CustomerInvoiceMail($invoice->id));
            }

            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'finance.invoice-sent',
                $invoice,
                $causer instanceof User ? $causer : null,
                ['invoice_number' => $invoice->invoice_number, 'total_cents' => $invoice->total_cents],
            );

            return $invoice->refresh();
        });
    }

    public function recordPayment(RecordPaymentData $data): Invoice
    {
        return DB::transaction(function () use ($data): Invoice {
            /** @var Invoice $invoice */
            $invoice = Invoice::query()->whereKey($data->invoiceId)->lockForUpdate()->firstOrFail();

            if ($data->amountCents <= 0) {
                throw ValidationException::withMessages(['amount' => 'Payment must be positive.']);
            }

            if ($data->amountCents > $invoice->openBalanceCents()) {
                throw ValidationException::withMessages(['amount' => 'Payment exceeds the open balance.']);
            }

            Payment::query()->create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->id,
                'amount_cents' => $data->amountCents,
                'payment_date' => $data->paymentDate ?? now()->toDateString(),
                'method' => $data->method,
                'reference' => $data->reference,
                'recorded_by' => Auth::id(),
            ]);

            $invoice->paid_amount_cents += $data->amountCents;

            $fullyPaid = $invoice->paid_amount_cents >= $invoice->total_cents;
            $invoice->status->transitionTo($fullyPaid ? Paid::class : PartiallyPaid::class);
            $invoice->save();

            // AR ↓ / bank ↑ — the ledger is the record, invoicing never writes it raw.
            $this->ledger->post(
                reference: ($invoice->invoice_number ?? $invoice->id).'-PAY',
                description: "Payment on invoice {$invoice->invoice_number}",
                entryDate: now(),
                lines: [
                    ['account_id' => LedgerService::accountIdByCode('1100'), 'debit_cents' => $data->amountCents],
                    ['account_id' => LedgerService::accountIdByCode('1200'), 'credit_cents' => $data->amountCents],
                ],
                sourceType: 'invoice-payment',
                sourceId: $invoice->id,
            );

            if ($fullyPaid) {
                $customer = $invoice->customer()->first();

                InvoicePaid::dispatch(
                    $invoice->company_id,
                    $invoice->id,
                    $invoice->customer_id,
                    $invoice->total_cents,
                    $invoice->currency,
                    $customer?->crm_account_id,
                );
            }

            return $invoice->refresh();
        });
    }

    public function void(string $invoiceId, string $reason): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::query()->findOrFail($invoiceId);

        $state = (string) $invoice->status;

        if (in_array($state, ['paid', 'partially_paid'], true)) {
            throw CannotVoidPaidInvoiceException::make();
        }

        $wasSent = in_array($state, ['sent', 'overdue'], true);

        $invoice->status->transitionTo(Voided::class);
        $invoice->update(['notes' => trim(($invoice->notes ?? '')."\nVoided: {$reason}")]);

        // A sent invoice already recognised revenue — mirror it out.
        if ($wasSent && $invoice->total_cents > 0) {
            $revenueEntry = JournalEntry::query()
                ->where('source_type', 'invoice')
                ->where('source_id', $invoice->id)
                ->first();

            if ($revenueEntry !== null) {
                $this->ledger->reverse($revenueEntry->id, "Void of {$invoice->invoice_number}: {$reason}");
            }
        }

        $causer = Auth::user();
        app(AuditLogger::class)->log(
            'finance.invoice-voided',
            $invoice,
            $causer instanceof User ? $causer : null,
            ['reason' => $reason],
        );

        return $invoice->refresh();
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        /** @var Collection<int, InvoiceLine> $lines */
        $lines = $invoice->lines()->get();

        $subtotal = Money::ofMinor(0, 'EUR');
        $tax = Money::ofMinor(0, 'EUR');

        foreach ($lines as $line) {
            $net = Money::ofMinor($line->line_total_cents - $line->tax_cents, 'EUR');
            $subtotal = $subtotal->plus($net);
            $tax = $tax->plus(Money::ofMinor($line->tax_cents, 'EUR'));
        }

        $total = $subtotal->plus($tax);

        if ((float) $invoice->discount_percent > 0) {
            $discount = $total->multipliedBy((string) ((float) $invoice->discount_percent / 100), RoundingMode::HalfUp);
            $total = $total->minus($discount);
        }

        $invoice->update([
            'subtotal_cents' => $subtotal->getMinorAmount()->toInt(),
            'tax_total_cents' => $tax->getMinorAmount()->toInt(),
            'total_cents' => $total->getMinorAmount()->toInt(),
        ]);

        return $invoice->refresh();
    }

    /** Gap-free per-company sequence, race-safe under the send lock. */
    private function nextInvoiceNumber(string $companyId): string
    {
        $year = now()->format('Y');
        $prefix = "INV-{$year}-";

        $last = Invoice::query()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $next = $last === null ? 1 : ((int) substr((string) $last, strlen($prefix))) + 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public static function nextRecurringDate(CarbonImmutable $from, string $schedule): string
    {
        return match ($schedule) {
            'monthly' => $from->addMonthNoOverflow()->toDateString(),
            'quarterly' => $from->addMonthsNoOverflow(3)->toDateString(),
            'annually' => $from->addYearNoOverflow()->toDateString(),
            default => throw new \InvalidArgumentException("Unknown schedule [{$schedule}]."),
        };
    }
}
