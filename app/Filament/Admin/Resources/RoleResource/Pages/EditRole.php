<?php

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Permission;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $prefixes = [
            'platform', 'workspace', 'hr', 'projects', 'finance',
            'crm', 'marketing', 'operations', 'analytics', 'it',
            'legal', 'ecommerce', 'communications', 'learning',
        ];

        $assignedIds = $this->getRecord()
            ->permissions()
            ->pluck('id')
            ->toArray();

        foreach ($prefixes as $prefix) {
            $modulePermissionIds = Permission::where('guard_name', 'tenant')
                ->where('name', 'like', "{$prefix}.%")
                ->whereIn('id', $assignedIds)
                ->pluck('id')
                ->toArray();

            $data["permissions_{$prefix}"] = $modulePermissionIds;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncPermissionsFromModuleTabs();
    }

    private function syncPermissionsFromModuleTabs(): void
    {
        $selected = $this->collectSelectedPermissionIds();

        $this->getRecord()->syncPermissions(
            Permission::whereIn('id', $selected)->get()
        );
    }

    private function collectSelectedPermissionIds(): array
    {
        $prefixes = [
            'platform', 'workspace', 'hr', 'projects', 'finance',
            'crm', 'marketing', 'operations', 'analytics', 'it',
            'legal', 'ecommerce', 'communications', 'learning',
        ];

        $ids = [];

        foreach ($prefixes as $prefix) {
            $ids = array_merge($ids, $this->data["permissions_{$prefix}"] ?? []);
        }

        return $ids;
    }
}
