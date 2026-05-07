<?php

namespace App\Filament\Projects\Resources\DocumentFolderResource\Pages;

use App\Filament\Projects\Resources\DocumentFolderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentFolders extends ListRecords
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
