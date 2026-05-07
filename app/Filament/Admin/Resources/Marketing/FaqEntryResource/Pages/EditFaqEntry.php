<?php

namespace App\Filament\Admin\Resources\Marketing\FaqEntryResource\Pages;

use App\Filament\Admin\Resources\Marketing\FaqEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaqEntry extends EditRecord
{
    protected static string $resource = FaqEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
