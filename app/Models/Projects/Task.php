<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Enums\Projects\TaskPriority;
use App\Enums\Projects\TaskStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'parent_task_id',
        'priority',
        'status',
        'assignee_tenant_id',
        'due_date',
        'start_date',
        'estimated_hours',
        'is_recurring',
        'recurrence_rule',
    ];

    protected function casts(): array
    {
        return [
            'priority'     => TaskPriority::class,
            'status'       => TaskStatus::class,
            'due_date'     => 'date',
            'start_date'   => 'date',
            'is_recurring' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'priority', 'assignee_tenant_id'])
            ->logOnlyDirty();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'assignee_tenant_id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_assignments', 'task_id', 'label_id');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function automationLogs(): HasMany
    {
        return $this->hasMany(TaskAutomationLog::class);
    }
}
