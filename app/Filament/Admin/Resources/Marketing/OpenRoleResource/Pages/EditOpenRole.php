<?php

namespace App\Filament\Admin\Resources\Marketing\OpenRoleResource\Pages;

use App\Filament\Admin\Resources\Marketing\OpenRoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpenRole extends EditRecord
{
    protected static string $resource = OpenRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
