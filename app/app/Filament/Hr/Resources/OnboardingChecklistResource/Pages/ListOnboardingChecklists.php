<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingChecklistResource\Pages;

use App\Filament\Hr\Resources\OnboardingChecklistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingChecklists extends ListRecords
{
    protected static string $resource = OnboardingChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
