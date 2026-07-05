<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeaveRequestResource\Pages;

use App\Filament\Hr\Resources\LeaveRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;
}
