<?php

namespace App\Notifications\Finance;

use App\Models\Finance\Invoice;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceOverdueNotification extends FlowFlexNotification
{
    public function __construct(public readonly Invoice $invoice) {}

    public function notificationType(): string
    {
        return 'finance.invoice.overdue';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice Overdue: ' . $this->invoice->number)
            ->greeting('Attention!')
            ->line("Invoice {$this->invoice->number} is now overdue.")
            ->line("Due date: {$this->invoice->due_date?->toDateString()}.")
            ->line("Amount outstanding: {$this->invoice->currency} {$this->invoice->total}.")
            ->action('View Invoice', url('/finance/invoices/' . $this->invoice->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => $this->notificationType(),
            'invoice_id' => $this->invoice->id,
            'number'     => $this->invoice->number,
            'due_date'   => $this->invoice->due_date?->toDateString(),
            'total'      => $this->invoice->total,
            'currency'   => $this->invoice->currency,
        ];
    }
}
