<?php

declare(strict_types=1);

namespace App\Mail\Finance;

use App\Actions\Finance\RenderCustomerInvoicePdfAction;
use App\Models\Finance\Invoice;
use App\Support\Mail\FlowFlexMailable;
use Brick\Money\Money;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Customer-facing invoice mail (finance.invoicing) with the PDF attached. */
class CustomerInvoiceMail extends FlowFlexMailable
{
    public function __construct(public readonly string $invoiceId)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        $invoice = Invoice::query()->find($this->invoiceId);

        return new Envelope(subject: 'Invoice '.($invoice->invoice_number ?? ''));
    }

    public function content(): Content
    {
        $invoice = Invoice::query()->with('customer')->findOrFail($this->invoiceId);

        return new Content(markdown: 'mail.finance.customer-invoice', with: [
            'number' => $invoice->invoice_number,
            'total' => Money::ofMinor($invoice->total_cents, $invoice->currency)->formatToLocale('nl_NL'),
            'dueDate' => $invoice->due_date->format('d M Y'),
            'customerName' => $invoice->customer()->first()->name ?? '',
        ]);
    }

    /** @return list<Attachment> */
    public function attachments(): array
    {
        $invoice = Invoice::query()->with(['lines', 'customer'])->find($this->invoiceId);

        if (! $invoice instanceof Invoice) {
            return [];
        }

        return [
            Attachment::fromData(
                fn (): string => RenderCustomerInvoicePdfAction::run($invoice),
                ($invoice->invoice_number ?? 'invoice').'.pdf',
            )->withMime('application/pdf'),
        ];
    }
}
