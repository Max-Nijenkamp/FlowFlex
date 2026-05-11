<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\ProjectMilestoneResource\Pages;

use App\Filament\Projects\Resources\ProjectMilestoneResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectMilestone extends CreateRecord
{
    protected static string $resource = ProjectMilestoneResource::class;
}
