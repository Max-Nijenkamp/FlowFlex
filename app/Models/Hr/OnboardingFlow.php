<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\OnboardingFlowStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class OnboardingFlow extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_id',
        'template_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => OnboardingFlowStatus::class,
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'started_at', 'completed_at'])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTask::class, 'flow_id');
    }

    public function checkins(): HasMany
    {
        // NOTE: The onboarding_checkins table has no flow_id FK — only employee_id.
        // This relationship returns ALL checkins for the employee, not just those
        // belonging to this specific flow instance. A future migration should add a
        // nullable flow_id column to onboarding_checkins to allow proper scoping.
        return $this->hasMany(OnboardingCheckin::class, 'employee_id', 'employee_id');
    }

    public function progressPercentage(): int
    {
        $total     = $this->tasks()->count();
        $completed = $this->tasks()->where('status', 'completed')->count();

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }
}
