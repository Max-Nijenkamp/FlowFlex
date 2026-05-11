<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\KanbanBoardResource\Pages;

use App\Filament\Projects\Resources\KanbanBoardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKanbanBoards extends ListRecords
{
    protected static string $resource = KanbanBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
