<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\ProjectMilestoneResource\Pages;

use App\Filament\Projects\Resources\ProjectMilestoneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectMilestone extends EditRecord
{
    protected static string $resource = ProjectMilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
