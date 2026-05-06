<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\LeaveAccrualType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LeavePolicy extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'leave_type_id',
        'accrual_type',
        'annual_entitlement_days',
        'max_carry_over_days',
        'allow_negative',
        'probation_restriction_months',
    ];

    protected function casts(): array
    {
        return [
            'accrual_type'             => LeaveAccrualType::class,
            'annual_entitlement_days'  => 'decimal:2',
            'max_carry_over_days'      => 'decimal:2',
            'allow_negative'           => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['accrual_type', 'annual_entitlement_days', 'max_carry_over_days'])
            ->logOnlyDirty();
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
