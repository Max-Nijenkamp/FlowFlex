<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\SprintResource\Pages;

use App\Filament\Projects\Resources\SprintResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSprint extends EditRecord
{
    protected static string $resource = SprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
