<?php

namespace App\Filament\Projects\Resources\TaskLabelResource\Pages;

use App\Filament\Projects\Resources\TaskLabelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskLabel extends EditRecord
{
    protected static string $resource = TaskLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
