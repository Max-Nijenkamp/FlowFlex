<?php

namespace App\Filament\Hr\Resources\PublicHolidayResource\Pages;

use App\Filament\Hr\Resources\PublicHolidayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicHoliday extends EditRecord
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
