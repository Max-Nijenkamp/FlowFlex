<?php

namespace App\Filament\Admin\Resources\Marketing\ChangelogEntryResource\Pages;

use App\Filament\Admin\Resources\Marketing\ChangelogEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChangelogEntries extends ListRecords
{
    protected static string $resource = ChangelogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
