<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LeaveType extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'color',
        'is_paid',
        'requires_approval',
        'min_notice_days',
        'allow_half_day',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid'           => 'boolean',
            'requires_approval' => 'boolean',
            'allow_half_day'    => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'is_paid', 'requires_approval', 'is_active'])
            ->logOnlyDirty();
    }

    public function policy(): HasOne
    {
        return $this->hasOne(LeavePolicy::class, 'leave_type_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }
}
