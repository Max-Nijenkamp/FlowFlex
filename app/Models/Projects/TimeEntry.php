<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TimeEntry extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'tenant_id',
        'task_id',
        'description',
        'entry_date',
        'minutes',
        'is_billable',
        'is_approved',
        'approved_by_tenant_id',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'entry_date'  => 'date',
            'is_billable' => 'boolean',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tenant_id', 'task_id', 'entry_date', 'minutes', 'is_billable', 'is_approved'])
            ->logOnlyDirty();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'approved_by_tenant_id');
    }

    public function hoursDecimal(): float
    {
        return round($this->minutes / 60, 2);
    }
}
