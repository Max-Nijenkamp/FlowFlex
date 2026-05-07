<?php

namespace App\Filament\Crm\Resources\TicketSlaRuleResource\Pages;

use App\Filament\Crm\Resources\TicketSlaRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTicketSlaRule extends EditRecord
{
    protected static string $resource = TicketSlaRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
