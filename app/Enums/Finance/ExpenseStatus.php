<?php

namespace App\Enums\Finance;

enum ExpenseStatus: string
{
    case Pending    = 'pending';
    case Approved   = 'approved';
    case Rejected   = 'rejected';
    case Reimbursed = 'reimbursed';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::Approved   => 'Approved',
            self::Rejected   => 'Rejected',
            self::Reimbursed => 'Reimbursed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending    => 'warning',
            self::Approved   => 'success',
            self::Rejected   => 'danger',
            self::Reimbursed => 'info',
        };
    }
}
