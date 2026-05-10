<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiClientResource\Pages;

use App\Filament\App\Resources\ApiClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiClients extends ListRecords
{
    protected static string $resource = ApiClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New API Client'),
        ];
    }
}
