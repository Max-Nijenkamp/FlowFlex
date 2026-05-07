<?php

namespace App\Listeners\Crm;

use App\Events\Crm\TicketResolved;
use App\Models\Tenant;
use App\Notifications\Crm\TicketResolvedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTicketResolved implements ShouldQueue
{
    public function handle(TicketResolved $event): void
    {
        $ticket = $event->ticket;

        // Notify the assigned agent if one is assigned
        if ($ticket->assigned_to) {
            $agent = Tenant::find($ticket->assigned_to);
            $agent?->notify(new TicketResolvedNotification($ticket));
        }
    }
}
