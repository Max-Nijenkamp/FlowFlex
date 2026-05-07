<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class TaskAutomationLog extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'automation_id',
        'task_id',
        'triggered_at',
        'success',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'triggered_at' => 'datetime',
            'success'      => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['automation_id', 'task_id', 'success'])
            ->logOnlyDirty();
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(TaskAutomation::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
