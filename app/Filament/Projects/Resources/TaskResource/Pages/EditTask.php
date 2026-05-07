<?php

namespace App\Filament\Projects\Resources\TaskResource\Pages;

use App\Events\Projects\TaskAssigned;
use App\Filament\Projects\Resources\TaskResource;
use App\Models\Tenant;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('assignee_tenant_id') && $this->record->assignee_tenant_id) {
            $assignee = Tenant::find($this->record->assignee_tenant_id);
            event(new TaskAssigned($this->record, $assignee));
        }
    }
}
