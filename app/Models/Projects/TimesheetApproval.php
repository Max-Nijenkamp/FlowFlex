<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TimesheetApproval extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity;

    protected $fillable = [
        'company_id',
        'timesheet_id',
        'approver_tenant_id',
        'status',
        'notes',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['timesheet_id', 'approver_tenant_id', 'status'])
            ->logOnlyDirty();
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'approver_tenant_id');
    }
}
