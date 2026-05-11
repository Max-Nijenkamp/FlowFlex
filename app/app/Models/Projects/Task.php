<?php

declare(strict_types=1);

namespace App\Models\Projects;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'company_id',
        'project_id',
        'parent_id',
        'title',
        'description',
        'assignee_id',
        'created_by',
        'status',
        'priority',
        'due_date',
        'start_date',
        'estimate_hours',
        'story_points',
        'labels',
        'sort_order',
        'is_recurring',
        'recurrence_rule',
        'completed_at',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'start_date'     => 'date',
        'completed_at'   => 'datetime',
        'estimate_hours' => 'decimal:1',
        'labels'         => 'array',
        'is_recurring'   => 'boolean',
        'recurrence_rule' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function sprint(): BelongsToMany
    {
        return $this->belongsToMany(Sprint::class, 'sprint_tasks');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }
}
