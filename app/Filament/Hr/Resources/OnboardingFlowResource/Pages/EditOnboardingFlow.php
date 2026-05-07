<?php

namespace App\Filament\Hr\Resources\OnboardingFlowResource\Pages;

use App\Filament\Hr\Resources\OnboardingFlowResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOnboardingFlow extends EditRecord
{
    protected static string $resource = OnboardingFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
