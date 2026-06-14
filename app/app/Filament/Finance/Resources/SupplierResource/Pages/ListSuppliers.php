<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\SupplierResource\Pages;

use App\Filament\Finance\Resources\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
