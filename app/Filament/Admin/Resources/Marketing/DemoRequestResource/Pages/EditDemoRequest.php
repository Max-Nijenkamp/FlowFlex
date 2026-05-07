<?php

namespace App\Filament\Admin\Resources\Marketing\DemoRequestResource\Pages;

use App\Filament\Admin\Resources\Marketing\DemoRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDemoRequest extends EditRecord
{
    protected static string $resource = DemoRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
