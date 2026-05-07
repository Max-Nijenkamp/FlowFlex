<?php

namespace App\Filament\Admin\Resources\Marketing\HelpCategoryResource\Pages;

use App\Filament\Admin\Resources\Marketing\HelpCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHelpCategory extends EditRecord
{
    protected static string $resource = HelpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
