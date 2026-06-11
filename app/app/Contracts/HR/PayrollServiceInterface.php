<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\CreatePayrollRunData;
use App\Models\HR\PayrollRun;

interface PayrollServiceInterface
{
    public function createRun(CreatePayrollRunData $data): PayrollRun;

    /** Generates payslips; throws IncompletePayrollProfileException listing blockers. */
    public function processRun(string $runId): PayrollRun;

    /** Four-eyes: approver != creator. Fires PayrollRunApproved. */
    public function approveRun(string $runId): PayrollRun;
}
