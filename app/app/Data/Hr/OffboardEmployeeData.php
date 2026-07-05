<?php

declare(strict_types=1);

namespace App\Data\Hr;

use Spatie\LaravelData\Data;

class OffboardEmployeeData extends Data
{
    public function __construct(
        public string $employeeId,
        public string $terminationDate,
        public string $reason,
    ) {}
}
