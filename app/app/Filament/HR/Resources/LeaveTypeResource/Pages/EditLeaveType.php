<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\LeaveTypeResource\Pages;

use App\Filament\HR\Resources\LeaveTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;
}
