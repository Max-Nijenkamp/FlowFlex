<?php

namespace App\Filament\Projects\Resources\DocumentFolderResource\Pages;

use App\Filament\Projects\Resources\DocumentFolderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentFolder extends EditRecord
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
