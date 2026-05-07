<?php

namespace App\Filament\Hr\Resources\PayElementResource\Pages;

use App\Filament\Hr\Resources\PayElementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayElement extends EditRecord
{
    protected static string $resource = PayElementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
