<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\MeetingTypeResource\Pages;

use App\Filament\CRM\Resources\MeetingTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingTypes extends ListRecords
{
    protected static string $resource = MeetingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
