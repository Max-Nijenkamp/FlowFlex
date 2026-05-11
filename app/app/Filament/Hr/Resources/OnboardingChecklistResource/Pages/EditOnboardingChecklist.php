<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingChecklistResource\Pages;

use App\Filament\Hr\Resources\OnboardingChecklistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOnboardingChecklist extends EditRecord
{
    protected static string $resource = OnboardingChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
