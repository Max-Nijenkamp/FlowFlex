<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages;

use App\Filament\Admin\Resources\CompanyFeatureFlagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyFeatureFlag extends CreateRecord
{
    protected static string $resource = CompanyFeatureFlagResource::class;
}
