<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\OnboardingTaskType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class OnboardingTemplateTask extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'template_id',
        'title',
        'description',
        'task_type',
        'default_assignee',
        'due_day_offset',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'task_type'   => OnboardingTaskType::class,
            'is_required' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'task_type', 'due_day_offset', 'is_required', 'sort_order'])
            ->logOnlyDirty();
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }
}
