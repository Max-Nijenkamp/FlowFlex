<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\OnboardingPlanResource\Pages;

use App\Filament\HR\Resources\OnboardingPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingPlans extends ListRecords
{
    protected static string $resource = OnboardingPlanResource::class;
}
