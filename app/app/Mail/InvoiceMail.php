<?php

declare(strict_types=1);

namespace App\Mail;

use App\Actions\RenderInvoicePdfAction;
use App\Models\BillingInvoice;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceMail extends FlowFlexMailable
{
    public function __construct(
        public readonly string $invoiceId,
        public readonly string $formattedTotal,
        public readonly string $periodLabel,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your FlowFlex invoice for {$this->periodLabel}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.invoice');
    }

    /**
     * PDF rendered at send time inside the queued job — only the invoice id
     * is serialized, and the renderer works without tenant context.
     *
     * @return list<Attachment>
     */
    public function attachments(): array
    {
        $invoice = BillingInvoice::query()->with('lines')->find($this->invoiceId);

        if (! $invoice instanceof BillingInvoice) {
            return [];
        }

        return [
            Attachment::fromData(
                fn (): string => RenderInvoicePdfAction::run($invoice),
                RenderInvoicePdfAction::number($invoice).'.pdf',
            )->withMime('application/pdf'),
        ];
    }
}
