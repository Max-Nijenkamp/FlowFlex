<?php

namespace App\Filament\Admin\Resources\Marketing\FaqEntryResource\Pages;

use App\Filament\Admin\Resources\Marketing\FaqEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqEntries extends ListRecords
{
    protected static string $resource = FaqEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
