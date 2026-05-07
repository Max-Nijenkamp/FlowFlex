<?php

namespace App\Filament\Admin\Resources\Marketing\ChangelogEntryResource\Pages;

use App\Filament\Admin\Resources\Marketing\ChangelogEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChangelogEntry extends EditRecord
{
    protected static string $resource = ChangelogEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
