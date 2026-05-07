<?php

namespace App\Filament\Hr\Resources\OnboardingTemplateResource\Pages;

use App\Filament\Hr\Resources\OnboardingTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOnboardingTemplate extends EditRecord
{
    protected static string $resource = OnboardingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
