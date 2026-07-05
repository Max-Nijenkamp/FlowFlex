<?php

declare(strict_types=1);

namespace App\Data\Hr;

use Spatie\LaravelData\Data;

class SubmitLeaveRequestData extends Data
{
    public function __construct(
        public string $employeeId,
        public string $leaveTypeId,
        public string $startDate,
        public string $endDate,
        public ?string $note = null,
    ) {}
}
