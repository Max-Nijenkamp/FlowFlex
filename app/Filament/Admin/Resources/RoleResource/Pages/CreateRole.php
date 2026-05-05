<?php

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Permission;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = 'tenant';

        return $data;
    }

    protected function afterCreate(): void
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
