<?php

namespace App\Events\Hr;

use App\Models\Hr\Payslip;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayslipGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Payslip $payslip) {}
}
