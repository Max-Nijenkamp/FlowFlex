<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ChartOfAccountsResource\Pages;

use App\Filament\Finance\Resources\ChartOfAccountsResource;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = ChartOfAccountsResource::class;
}
