<?php

namespace App\Filament\Crm\Resources\ChatbotRuleResource\Pages;

use App\Filament\Crm\Resources\ChatbotRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChatbotRule extends EditRecord
{
    protected static string $resource = ChatbotRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
