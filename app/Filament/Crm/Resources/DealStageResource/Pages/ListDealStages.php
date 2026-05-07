<?php

namespace App\Filament\Crm\Resources\DealStageResource\Pages;

use App\Filament\Crm\Resources\DealStageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDealStages extends ListRecords
{
    protected static string $resource = DealStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
