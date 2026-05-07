<?php

namespace App\Filament\Hr\Resources\PayElementResource\Pages;

use App\Filament\Hr\Resources\PayElementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayElements extends ListRecords
{
    protected static string $resource = PayElementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
