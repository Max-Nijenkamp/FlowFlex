<?php

declare(strict_types=1);

namespace App\Contracts\Projects;

use App\Data\Projects\LogTimeData;
use App\Models\Projects\TimeEntry;
use App\Models\User;

interface TimeEntryServiceInterface
{
    public function log(LogTimeData $data): TimeEntry;

    public function approve(TimeEntry $timeEntry, User $approver): TimeEntry;

    public function calculateHours(string $userId, string $projectId = null, string $startDate = null, string $endDate = null): float;
}
