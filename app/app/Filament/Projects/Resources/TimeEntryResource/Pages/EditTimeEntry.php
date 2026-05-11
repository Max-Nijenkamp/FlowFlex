<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\TimeEntryResource\Pages;

use App\Filament\Projects\Resources\TimeEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTimeEntry extends EditRecord
{
    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
