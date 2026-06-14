<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\BudgetResource\Pages;

use App\Filament\Finance\Resources\BudgetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
