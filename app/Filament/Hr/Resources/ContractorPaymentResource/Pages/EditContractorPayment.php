<?php

namespace App\Filament\Hr\Resources\ContractorPaymentResource\Pages;

use App\Filament\Hr\Resources\ContractorPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContractorPayment extends EditRecord
{
    protected static string $resource = ContractorPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
