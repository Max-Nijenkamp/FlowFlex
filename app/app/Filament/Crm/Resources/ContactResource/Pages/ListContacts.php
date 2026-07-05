<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\ContactResource\Pages;

use App\Filament\Crm\Resources\ContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
