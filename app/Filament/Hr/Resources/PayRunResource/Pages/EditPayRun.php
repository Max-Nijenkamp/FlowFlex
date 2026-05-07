<?php

namespace App\Filament\Hr\Resources\PayRunResource\Pages;

use App\Filament\Hr\Resources\PayRunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayRun extends EditRecord
{
    protected static string $resource = PayRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
