<?php

namespace App\Enums\Finance;

enum InvoiceStatus: string
{
    case Draft         = 'draft';
    case Sent          = 'sent';
    case PartiallyPaid = 'partially_paid';
    case Paid          = 'paid';
    case Overdue       = 'overdue';
    case WrittenOff    = 'written_off';

    public function label(): string
    {
        return match($this) {
            self::Draft         => 'Draft',
            self::Sent          => 'Sent',
            self::PartiallyPaid => 'Partially Paid',
            self::Paid          => 'Paid',
            self::Overdue       => 'Overdue',
            self::WrittenOff    => 'Written Off',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft         => 'gray',
            self::Sent          => 'info',
            self::PartiallyPaid => 'warning',
            self::Paid          => 'success',
            self::Overdue       => 'danger',
            self::WrittenOff    => 'gray',
        };
    }
}
