<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingPlanResource\Pages;

use App\Filament\Hr\Resources\OnboardingPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingPlans extends ListRecords
{
    protected static string $resource = OnboardingPlanResource::class;
}
