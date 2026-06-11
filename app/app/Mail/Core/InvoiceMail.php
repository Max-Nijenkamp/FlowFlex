<?php

declare(strict_types=1);

namespace App\Mail\Core;

use App\Data\Core\BillingInvoiceData;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceMail extends FlowFlexMailable
{
    public function __construct(
        public readonly string $company_id,
        public readonly BillingInvoiceData $invoice,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your FlowFlex invoice — {$this->invoice->period_start}");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.core.invoice',
            with: [
                ...$this->branding(),
                'invoice' => $this->invoice,
            ],
        );
    }
}
