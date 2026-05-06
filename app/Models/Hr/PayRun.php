<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PayRun extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'payroll_entity_id',
        'status',
        'pay_frequency',
        'pay_period_start',
        'pay_period_end',
        'payment_date',
        'total_gross',
        'total_net',
        'total_deductions',
        'created_by_tenant_id',
        'approved_by_tenant_id',
        'approved_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'           => PayRunStatus::class,
            'pay_frequency'    => PayFrequency::class,
            'pay_period_start' => 'date',
            'pay_period_end'   => 'date',
            'payment_date'     => 'date',
            'total_gross'      => 'decimal:2',
            'total_net'        => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'approved_at'      => 'datetime',
            'processed_at'     => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total_gross', 'total_net', 'approved_by_tenant_id', 'processed_at'])
            ->logOnlyDirty();
    }

    public function payrollEntity(): BelongsTo
    {
        return $this->belongsTo(PayrollEntity::class, 'payroll_entity_id');
    }

    public function runEmployees(): HasMany
    {
        return $this->hasMany(PayRunEmployee::class, 'pay_run_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'pay_run_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'created_by_tenant_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'approved_by_tenant_id');
    }
}
