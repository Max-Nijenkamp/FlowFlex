<?php

namespace App\Filament\Crm\Resources\ChatbotRuleResource\Pages;

use App\Filament\Crm\Resources\ChatbotRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatbotRules extends ListRecords
{
    protected static string $resource = ChatbotRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
