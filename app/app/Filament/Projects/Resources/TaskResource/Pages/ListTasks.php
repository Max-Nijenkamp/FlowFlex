<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\TaskResource\Pages;

use App\Filament\Projects\Resources\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
