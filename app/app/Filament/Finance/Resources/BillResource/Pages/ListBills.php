<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\BillResource\Pages;

use App\Filament\Finance\Resources\BillResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
