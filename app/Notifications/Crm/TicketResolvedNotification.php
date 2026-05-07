<?php

namespace App\Notifications\Crm;

use App\Models\Crm\Ticket;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketResolvedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Ticket $ticket) {}

    public function notificationType(): string
    {
        return 'crm.ticket.resolved';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket Resolved: ' . $this->ticket->subject)
            ->greeting('Ticket Update')
            ->line("Ticket #{$this->ticket->id} has been resolved.")
            ->line("Subject: {$this->ticket->subject}.")
            ->line("Resolved at: {$this->ticket->resolved_at?->toDateTimeString()}.")
            ->action('View Ticket', url('/crm/tickets/' . $this->ticket->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => $this->notificationType(),
            'ticket_id'   => $this->ticket->id,
            'subject'     => $this->ticket->subject,
            'resolved_at' => $this->ticket->resolved_at?->toDateTimeString(),
        ];
    }
}
