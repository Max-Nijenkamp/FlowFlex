<?php

namespace App\Filament\Finance\Resources\RecurringInvoiceResource\Pages;

use App\Filament\Finance\Resources\RecurringInvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecurringInvoice extends EditRecord
{
    protected static string $resource = RecurringInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
