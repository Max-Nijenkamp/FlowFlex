<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExpenseReportResource\Pages;

use App\Filament\Finance\Resources\ExpenseReportResource;
use Filament\Resources\Pages\EditRecord;

class EditExpenseReport extends EditRecord
{
    protected static string $resource = ExpenseReportResource::class;
}
