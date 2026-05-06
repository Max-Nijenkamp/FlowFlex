<?php

namespace App\Enums\Hr;

enum PayRunStatus: string
{
    case Draft           = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved        = 'approved';
    case Processing      = 'processing';
    case Processed       = 'processed';
    case Failed          = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Draft           => 'Draft',
            self::PendingApproval => 'Pending Approval',
            self::Approved        => 'Approved',
            self::Processing      => 'Processing',
            self::Processed       => 'Processed',
            self::Failed          => 'Failed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft           => 'gray',
            self::PendingApproval => 'warning',
            self::Approved        => 'info',
            self::Processing      => 'info',
            self::Processed       => 'success',
            self::Failed          => 'danger',
        };
    }
}
