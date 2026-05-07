<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class TaskAutomation extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'trigger_type',
        'trigger_conditions',
        'action_type',
        'action_config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'trigger_conditions' => 'array',
            'action_config'      => 'array',
            'is_active'          => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'trigger_type', 'action_type', 'is_active'])
            ->logOnlyDirty();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TaskAutomationLog::class, 'automation_id');
    }
}
