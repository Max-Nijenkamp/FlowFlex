<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\OnboardingTaskStatus;
use App\Enums\Hr\OnboardingTaskType;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class OnboardingTask extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'flow_id',
        'template_task_id',
        'title',
        'description',
        'task_type',
        'assigned_to_tenant_id',
        'due_date',
        'status',
        'completed_at',
        'completion_notes',
    ];

    protected function casts(): array
    {
        return [
            'task_type'    => OnboardingTaskType::class,
            'status'       => OnboardingTaskStatus::class,
            'due_date'     => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_to_tenant_id', 'due_date', 'completed_at'])
            ->logOnlyDirty();
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(OnboardingFlow::class, 'flow_id');
    }

    public function templateTask(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplateTask::class, 'template_task_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'assigned_to_tenant_id');
    }
}
