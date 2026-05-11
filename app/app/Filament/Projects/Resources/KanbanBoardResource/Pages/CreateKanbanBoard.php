<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\KanbanBoardResource\Pages;

use App\Filament\Projects\Resources\KanbanBoardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKanbanBoard extends CreateRecord
{
    protected static string $resource = KanbanBoardResource::class;
}
