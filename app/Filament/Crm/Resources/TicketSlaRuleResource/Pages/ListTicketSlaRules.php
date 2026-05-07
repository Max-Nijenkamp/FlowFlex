<?php

namespace App\Filament\Crm\Resources\TicketSlaRuleResource\Pages;

use App\Filament\Crm\Resources\TicketSlaRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketSlaRules extends ListRecords
{
    protected static string $resource = TicketSlaRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
