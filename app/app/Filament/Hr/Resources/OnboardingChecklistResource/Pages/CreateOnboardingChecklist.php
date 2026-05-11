<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingChecklistResource\Pages;

use App\Filament\Hr\Resources\OnboardingChecklistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOnboardingChecklist extends CreateRecord
{
    protected static string $resource = OnboardingChecklistResource::class;
}
