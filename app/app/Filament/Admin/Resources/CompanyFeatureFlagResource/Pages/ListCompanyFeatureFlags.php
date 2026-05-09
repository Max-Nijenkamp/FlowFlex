<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages;

use App\Filament\Admin\Resources\CompanyFeatureFlagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyFeatureFlags extends ListRecords
{
    protected static string $resource = CompanyFeatureFlagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
