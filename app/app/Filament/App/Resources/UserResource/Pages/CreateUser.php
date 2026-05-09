<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = app(CompanyContext::class)->current();
        $data['company_id'] = $company->id;
        $data['status'] = 'invited';

        return $data;
    }
}
