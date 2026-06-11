<?php

declare(strict_types=1);

namespace App\Data\HR;

use App\Models\HR\LeaveRequest;
use Spatie\LaravelData\Data;

class LeaveRequestData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $employee_id,
        public readonly string $leave_type_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly float $days_requested,
        public readonly string $status,
        public readonly ?string $note,
        public readonly ?string $approved_by,
        public readonly ?string $rejection_reason,
    ) {}

    public static function fromModel(LeaveRequest $request): self
    {
        return new self(
            id: $request->id,
            employee_id: $request->employee_id,
            leave_type_id: $request->leave_type_id,
            start_date: $request->start_date->toDateString(),
            end_date: $request->end_date->toDateString(),
            days_requested: $request->days_requested,
            status: (string) $request->status,
            note: $request->note,
            approved_by: $request->approved_by,
            rejection_reason: $request->rejection_reason,
        );
    }
}
