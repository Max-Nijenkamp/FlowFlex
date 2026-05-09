<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Data\Foundation\UpdateCompanyData;
use App\Filament\Admin\Resources\CompanyResource;
use App\Services\Foundation\CompanyService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $updateData = new UpdateCompanyData(
            name:      $data['name'],
            slug:      $data['slug'],
            email:     $data['email'],
            timezone:  $data['timezone'],
            locale:    $data['locale'],
            currency:  $data['currency'],
            branding:  $data['branding'] ?? null,
            ai_config: $data['ai_config'] ?? null,
        );

        return app(CompanyService::class)->update($record->id, $updateData);
    }
}
