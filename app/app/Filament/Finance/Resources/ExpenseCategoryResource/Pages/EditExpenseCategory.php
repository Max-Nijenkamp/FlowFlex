<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Finance\Resources\ExpenseCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditExpenseCategory extends EditRecord
{
    protected static string $resource = ExpenseCategoryResource::class;
}
