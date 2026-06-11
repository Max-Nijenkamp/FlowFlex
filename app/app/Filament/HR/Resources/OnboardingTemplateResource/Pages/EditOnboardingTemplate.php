<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\OnboardingTemplateResource\Pages;

use App\Filament\HR\Resources\OnboardingTemplateResource;
use Filament\Resources\Pages\EditRecord;

class EditOnboardingTemplate extends EditRecord
{
    protected static string $resource = OnboardingTemplateResource::class;
}
