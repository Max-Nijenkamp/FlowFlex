<?php

namespace App\Enums\Hr;

enum PayElementType: string
{
    case BasicSalary  = 'basic_salary';
    case Overtime     = 'overtime';
    case Bonus        = 'bonus';
    case Deduction    = 'deduction';
    case Reimbursement = 'reimbursement';
    case Commission   = 'commission';

    public function label(): string
    {
        return match($this) {
            self::BasicSalary   => 'Basic Salary',
            self::Overtime      => 'Overtime',
            self::Bonus         => 'Bonus',
            self::Deduction     => 'Deduction',
            self::Reimbursement => 'Reimbursement',
            self::Commission    => 'Commission',
        };
    }
}
