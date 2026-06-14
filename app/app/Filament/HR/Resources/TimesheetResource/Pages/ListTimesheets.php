<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\TimesheetResource\Pages;

use App\Filament\HR\Resources\TimesheetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
