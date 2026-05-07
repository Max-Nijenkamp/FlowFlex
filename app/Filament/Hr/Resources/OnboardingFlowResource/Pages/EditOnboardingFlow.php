<?php

namespace App\Filament\Hr\Resources\OnboardingFlowResource\Pages;

use App\Events\Hr\OnboardingStarted;
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

    protected function afterSave(): void
    {
        if (
            $this->record->wasChanged('status')
            && $this->record->status->value === 'in_progress'
        ) {
            event(new OnboardingStarted($this->record));
        }
    }
}
