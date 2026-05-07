<?php

namespace App\Filament\Hr\Resources\LeaveRequestResource\Pages;

use App\Events\Hr\LeaveRequested;
use App\Filament\Hr\Resources\LeaveRequestResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);

        $data['total_days'] = ($data['is_half_day'] ?? false)
            ? 0.5
            : (float) $start->diffInWeekdays($end) + 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        event(new LeaveRequested($this->record));
    }
}
