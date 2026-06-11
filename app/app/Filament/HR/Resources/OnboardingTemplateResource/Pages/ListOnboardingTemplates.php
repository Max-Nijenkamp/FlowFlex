<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\OnboardingTemplateResource\Pages;

use App\Filament\HR\Resources\OnboardingTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingTemplates extends ListRecords
{
    protected static string $resource = OnboardingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
