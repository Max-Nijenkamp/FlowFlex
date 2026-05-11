<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\KanbanBoardResource\Pages;

use App\Filament\Projects\Resources\KanbanBoardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKanbanBoard extends EditRecord
{
    protected static string $resource = KanbanBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
