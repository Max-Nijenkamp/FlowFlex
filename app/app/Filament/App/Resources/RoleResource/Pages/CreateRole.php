<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RoleResource\Pages;

use App\Filament\App\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = 'web';
        $data['team_id'] = getPermissionsTeamId();

        return $data;
    }
}
