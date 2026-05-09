<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages;

use App\Filament\Admin\Resources\CompanyFeatureFlagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyFeatureFlag extends EditRecord
{
    protected static string $resource = CompanyFeatureFlagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
