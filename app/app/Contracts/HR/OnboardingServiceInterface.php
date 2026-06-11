<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Models\HR\OnboardingPlan;

interface OnboardingServiceInterface
{
    /** Dept template → company default → null (no-op). */
    public function startPlan(string $companyId, string $employeeId, ?string $templateId = null): ?OnboardingPlan;

    public function completeTask(string $planTaskId, string $status = 'complete'): void;

    public function progress(string $planId): float;
}
