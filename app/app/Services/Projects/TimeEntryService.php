<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Contracts\Projects\TimeEntryServiceInterface;
use App\Data\Projects\LogTimeData;
use App\Events\Projects\TimeEntryApproved;
use App\Models\Projects\TimeEntry;
use App\Models\User;

class TimeEntryService implements TimeEntryServiceInterface
{
    public function log(LogTimeData $data): TimeEntry
    {
        return TimeEntry::create([
            'user_id'      => $data->user_id,
            'task_id'      => $data->task_id,
            'project_id'   => $data->project_id,
            'date'         => $data->date,
            'hours'        => $data->hours,
            'description'  => $data->description,
            'is_billable'  => $data->is_billable,
            'billing_rate' => $data->billing_rate,
        ]);
    }

    public function approve(TimeEntry $timeEntry, User $approver): TimeEntry
    {
        $timeEntry->update([
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        event(new TimeEntryApproved($timeEntry->company, $timeEntry));

        return $timeEntry->fresh();
    }

    public function calculateHours(string $userId, string $projectId = null, string $startDate = null, string $endDate = null): float
    {
        $query = TimeEntry::withoutGlobalScopes()
            ->where('user_id', $userId);

        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }

        if ($startDate !== null) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate !== null) {
            $query->where('date', '<=', $endDate);
        }

        return (float) $query->sum('hours');
    }
}
