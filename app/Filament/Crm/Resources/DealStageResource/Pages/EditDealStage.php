<?php

namespace App\Filament\Crm\Resources\DealStageResource\Pages;

use App\Filament\Crm\Resources\DealStageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDealStage extends EditRecord
{
    protected static string $resource = DealStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
