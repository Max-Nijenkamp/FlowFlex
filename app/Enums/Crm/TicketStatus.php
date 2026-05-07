<?php

namespace App\Enums\Crm;

enum TicketStatus: string
{
    case Open            = 'open';
    case InProgress      = 'in_progress';
    case PendingCustomer = 'pending_customer';
    case Resolved        = 'resolved';
    case Closed          = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Open            => 'Open',
            self::InProgress      => 'In Progress',
            self::PendingCustomer => 'Pending Customer',
            self::Resolved        => 'Resolved',
            self::Closed          => 'Closed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Open            => 'danger',
            self::InProgress      => 'warning',
            self::PendingCustomer => 'info',
            self::Resolved        => 'success',
            self::Closed          => 'gray',
        };
    }
}
