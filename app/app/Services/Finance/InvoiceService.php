<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Events\Finance\InvoicePaid;
use App\Exceptions\Finance\CannotVoidPaidInvoiceException;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Models\Finance\JournalEntry;
use App\States\Finance\Invoice\Draft;
use App\States\Finance\Invoice\Paid;
use App\States\Finance\Invoice\PartiallyPaid;
use App\States\Finance\Invoice\Sent;
use App\States\Finance\Invoice\Voided;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private readonly LedgerServiceInterface $ledger,
    ) {}

    public function create(CreateInvoiceData $data): Invoice
    {
        $customer = Customer::query()->findOrFail($data->customer_id);

        return DB::transaction(function () use ($data, $customer): Invoice {
            $subtotal = Money::ofMinor(0, 'EUR');

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'issue_date' => $data->issue_date,
                'due_date' => $data->due_date
                    ?? CarbonImmutable::parse($data->issue_date)->addDays($customer->payment_terms_days)->toDateString(),
                'notes' => $data->notes,
                'source_deal_id' => $data->source_deal_id,
            ]);

            foreach ($data->lines as $line) {
                $lineTotal = Money::ofMinor($line['unit_price_cents'], 'EUR')
                    ->multipliedBy((string) $line['quantity'], RoundingMode::HALF_UP);
                $subtotal = $subtotal->plus($lineTotal);

                $invoice->lines()->create([
                    'company_id' => $invoice->company_id,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price_cents' => $line['unit_price_cents'],
                    'line_total_cents' => $lineTotal->getMinorAmount()->toInt(),
                ]);
            }

            $invoice->update([
                'subtotal_cents' => $subtotal->getMinorAmount()->toInt(),
                'total_cents' => $subtotal->getMinorAmount()->toInt(), // tax arrives with finance.tax
            ]);

            return $invoice->refresh();
        });
    }

    public function send(string $invoiceId): Invoice
    {
        $invoice = Invoice::query()->findOrFail($invoiceId);

        return DB::transaction(function () use ($invoice): Invoice {
            if ($invoice->invoice_number === null) {
                $invoice->update(['invoice_number' => $this->nextInvoiceNumber()]);
            }

            $invoice->status->transitionTo(Sent::class);

            // AR posting: receivable up / revenue up.
            $this->ledger->post(
                reference: $invoice->invoice_number,
                description: "Invoice {$invoice->invoice_number} sent",
                entryDate: now()->toDateString(),
                lines: [
                    ['account_code' => '1100', 'debit_cents' => $invoice->total_cents],
                    ['account_code' => '4000', 'credit_cents' => $invoice->total_cents],
                ],
                sourceType: Invoice::class,
                sourceId: $invoice->id,
            );

            return $invoice->refresh();
        });
    }

    public function recordPayment(RecordPaymentData $data): Invoice
    {
        $invoice = Invoice::query()->findOrFail($data->invoice_id);
        $remaining = $invoice->total_cents - $invoice->paid_amount_cents;

        if ($data->amount_cents > $remaining) {
            throw ValidationException::withMessages([
                'amount_cents' => "Payment exceeds the remaining balance of {$remaining} cents.",
            ]);
        }

        return DB::transaction(function () use ($invoice, $data, $remaining): Invoice {
            $invoice->payments()->create([
                'company_id' => $invoice->company_id,
                'amount_cents' => $data->amount_cents,
                'payment_date' => $data->payment_date,
                'payment_method' => $data->payment_method,
                'reference_number' => $data->reference_number,
            ]);

            $invoice->increment('paid_amount_cents', $data->amount_cents);

            // Cash up / receivable down.
            $this->ledger->post(
                reference: "PAY-{$invoice->invoice_number}",
                description: "Payment on invoice {$invoice->invoice_number}",
                entryDate: $data->payment_date,
                lines: [
                    ['account_code' => '1000', 'debit_cents' => $data->amount_cents],
                    ['account_code' => '1100', 'credit_cents' => $data->amount_cents],
                ],
                sourceType: Invoice::class,
                sourceId: $invoice->id,
            );

            $fullyPaid = $data->amount_cents === $remaining;
            $invoice->status->transitionTo($fullyPaid ? Paid::class : PartiallyPaid::class);

            if ($fullyPaid) {
                event(new InvoicePaid(
                    company_id: $invoice->company_id,
                    invoice_id: $invoice->id,
                    crm_account_id: $invoice->customer->crm_account_id,
                    amount_cents: $invoice->total_cents,
                    currency: $invoice->currency,
                    paid_at: CarbonImmutable::parse($data->payment_date),
                ));
            }

            return $invoice->refresh();
        });
    }

    public function void(string $invoiceId, string $reason): Invoice
    {
        $invoice = Invoice::query()->findOrFail($invoiceId);

        if ($invoice->paid_amount_cents > 0) {
            throw new CannotVoidPaidInvoiceException('Paid invoices cannot be voided.');
        }

        $wasSent = ! $invoice->status->equals(Draft::class);
        $invoice->status->transitionTo(Voided::class);

        // Reverse the AR posting if anything was posted.
        if ($wasSent && $invoice->invoice_number !== null) {
            $entry = JournalEntry::query()
                ->where('source_type', Invoice::class)
                ->where('source_id', $invoice->id)
                ->where('reference', $invoice->invoice_number)
                ->first();

            if ($entry !== null) {
                $this->ledger->reverse($entry->id, "Invoice voided: {$reason}");
            }
        }

        return $invoice->refresh();
    }

    /** Gap-free sequential per company, assigned at first send. */
    private function nextInvoiceNumber(): string
    {
        $year = now()->year;
        $max = Invoice::query()
            ->withTrashed()
            ->whereNotNull('invoice_number')
            ->where('invoice_number', 'like', "INV-{$year}-%")
            ->count();

        return sprintf('INV-%d-%03d', $year, $max + 1);
    }
}
