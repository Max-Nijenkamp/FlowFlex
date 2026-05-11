<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\TimeEntryResource\Pages;

use App\Filament\Projects\Resources\TimeEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
