<?php

namespace App\Filament\Hr\Resources\PublicHolidayResource\Pages;

use App\Filament\Hr\Resources\PublicHolidayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicHolidays extends ListRecords
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
