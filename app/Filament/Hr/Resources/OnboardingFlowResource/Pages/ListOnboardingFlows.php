<?php

namespace App\Filament\Hr\Resources\OnboardingFlowResource\Pages;

use App\Filament\Hr\Resources\OnboardingFlowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingFlows extends ListRecords
{
    protected static string $resource = OnboardingFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
