<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaskDependency extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity;

    protected $fillable = [
        'company_id',
        'task_id',
        'depends_on_task_id',
        'dependency_type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['task_id', 'depends_on_task_id', 'dependency_type'])
            ->logOnlyDirty();
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }
}
