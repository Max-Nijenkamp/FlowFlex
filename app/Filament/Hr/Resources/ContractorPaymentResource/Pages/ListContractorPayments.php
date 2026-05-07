<?php

namespace App\Filament\Hr\Resources\ContractorPaymentResource\Pages;

use App\Filament\Hr\Resources\ContractorPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContractorPayments extends ListRecords
{
    protected static string $resource = ContractorPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
