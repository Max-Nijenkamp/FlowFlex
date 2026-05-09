<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Data\Foundation\CreateCompanyData;
use App\Filament\Admin\Resources\CompanyResource;
use App\Services\Foundation\CompanyCreationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $createData = new CreateCompanyData(
            name:              $data['name'],
            slug:              $data['slug'],
            email:             $data['email'],
            timezone:          $data['timezone'],
            locale:            $data['locale'],
            currency:          $data['currency'],
            owner_first_name:  $data['owner_first_name'],
            owner_last_name:   $data['owner_last_name'],
            owner_email:       $data['owner_email'],
            country:           $data['country'] ?? null,
            starter_modules:   $data['starter_modules'] ?? null,
        );

        return app(CompanyCreationService::class)->create($createData);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
