<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingTemplateResource\Pages;

use App\Filament\Hr\Resources\OnboardingTemplateResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateOnboardingTemplate extends CreateRecord
{
    protected static string $resource = OnboardingTemplateResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        return $data;
    }
}
