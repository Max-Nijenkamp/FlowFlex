<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeavePolicyResource\Pages;

use App\Filament\Hr\Resources\LeavePolicyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeavePolicies extends ListRecords
{
    protected static string $resource = LeavePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
