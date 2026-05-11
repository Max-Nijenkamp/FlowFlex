<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Models\HR\Employee;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingChecklistItem;
use App\Models\HR\OnboardingTemplate;

interface OnboardingServiceInterface
{
    public function createChecklist(Employee $employee, OnboardingTemplate $template): OnboardingChecklist;

    public function completeItem(OnboardingChecklistItem $item): OnboardingChecklistItem;

    public function getProgress(OnboardingChecklist $checklist): array;
}
