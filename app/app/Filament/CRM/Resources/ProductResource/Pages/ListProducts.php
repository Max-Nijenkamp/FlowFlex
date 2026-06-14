<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ProductResource\Pages;

use App\Filament\CRM\Resources\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
