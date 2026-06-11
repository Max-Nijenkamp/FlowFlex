<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\TimesheetResource\Pages;

use App\Filament\HR\Resources\TimesheetResource;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;
}
