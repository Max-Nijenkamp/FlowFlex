<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\InvitationResource\Pages;

use App\Filament\App\Resources\InvitationResource;
use Filament\Resources\Pages\ListRecords;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;
}
