<?php

namespace App\Filament\Projects\Resources\TaskLabelResource\Pages;

use App\Filament\Projects\Resources\TaskLabelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskLabels extends ListRecords
{
    protected static string $resource = TaskLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
