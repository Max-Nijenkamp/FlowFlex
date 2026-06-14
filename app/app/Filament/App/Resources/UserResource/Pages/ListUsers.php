<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\InvitationResource;
use App\Filament\App\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        // Workspaces are invite-only — "creating a user" = sending an invitation.
        return [
            Action::make('invite')
                ->label('Invite teammate')
                ->icon('heroicon-o-envelope')
                ->url(InvitationResource::getUrl())
                ->visible(fn (): bool => InvitationResource::canAccess()),
        ];
    }
}
