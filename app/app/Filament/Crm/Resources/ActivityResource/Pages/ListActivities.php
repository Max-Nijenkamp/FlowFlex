<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\ActivityResource\Pages;

use App\Filament\Crm\Resources\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
