<?php

namespace App\Filament\Hr\Resources\OnboardingTemplateResource\Pages;

use App\Filament\Hr\Resources\OnboardingTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingTemplates extends ListRecords
{
    protected static string $resource = OnboardingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
