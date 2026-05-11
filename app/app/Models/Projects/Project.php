<?php

declare(strict_types=1);

namespace App\Models\Projects;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
        'priority',
        'owner_id',
        'start_date',
        'due_date',
        'completed_at',
        'budget',
        'color',
        'is_template',
        'template_id',
        'custom_fields',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'due_date'      => 'date',
        'completed_at'  => 'datetime',
        'budget'        => 'decimal:2',
        'is_template'   => 'boolean',
        'custom_fields' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(KanbanBoard::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'template_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
