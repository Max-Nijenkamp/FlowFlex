<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Enums\Projects\TimesheetStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Timesheet extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'tenant_id',
        'week_start_date',
        'status',
        'submitted_at',
        'total_minutes',
    ];

    protected function casts(): array
    {
        return [
            'status'          => TimesheetStatus::class,
            'week_start_date' => 'date',
            'submitted_at'    => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tenant_id', 'week_start_date', 'status', 'total_minutes'])
            ->logOnlyDirty();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TimesheetApproval::class);
    }
}
