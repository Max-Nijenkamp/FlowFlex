<?php

namespace App\Filament\Hr\Resources\PayRunResource\Pages;

use App\Filament\Hr\Resources\PayRunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayRuns extends ListRecords
{
    protected static string $resource = PayRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
