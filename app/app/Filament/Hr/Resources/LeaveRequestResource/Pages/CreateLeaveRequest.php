<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeaveRequestResource\Pages;

use App\Filament\Hr\Resources\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;
}
