<?php

namespace App\Filament\Projects\Resources\TaskResource\Pages;

use App\Events\Projects\TaskAssigned;
use App\Filament\Projects\Resources\TaskResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->assignee_tenant_id) {
            $assignee = Tenant::find($this->record->assignee_tenant_id);
            event(new TaskAssigned($this->record, $assignee));
        }
    }
}
