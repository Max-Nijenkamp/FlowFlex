<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\ProjectMilestoneResource\Pages;

use App\Filament\Projects\Resources\ProjectMilestoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectMilestones extends ListRecords
{
    protected static string $resource = ProjectMilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
