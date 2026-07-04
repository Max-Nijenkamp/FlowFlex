<?php

declare(strict_types=1);

namespace App\Mail;

use App\Support\Mail\FlowFlexMailable;
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
}
