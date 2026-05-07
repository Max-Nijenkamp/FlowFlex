<?php

namespace App\Filament\Admin\Resources\Marketing\TeamMemberResource\Pages;

use App\Filament\Admin\Resources\Marketing\TeamMemberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
