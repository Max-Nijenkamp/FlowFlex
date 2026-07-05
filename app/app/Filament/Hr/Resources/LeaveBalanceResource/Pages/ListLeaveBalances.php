<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeaveBalanceResource\Pages;

use App\Filament\Hr\Resources\LeaveBalanceResource;
use Filament\Resources\Pages\ListRecords;

class ListLeaveBalances extends ListRecords
{
    protected static string $resource = LeaveBalanceResource::class;
}
