<?php

namespace App\Filament\Finance\Resources\MileageRateResource\Pages;

use App\Filament\Finance\Resources\MileageRateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMileageRate extends EditRecord
{
    protected static string $resource = MileageRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
