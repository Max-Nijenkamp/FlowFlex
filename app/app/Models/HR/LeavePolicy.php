<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeavePolicy extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'leave_policies';

    protected $fillable = [
        'company_id',
        'name',
        'leave_type',
        'days_per_year',
        'carry_over_days',
        'is_paid',
        'requires_approval',
        'min_notice_days',
        'is_active',
    ];

    protected $casts = [
        'days_per_year'    => 'decimal:1',
        'carry_over_days'  => 'decimal:1',
        'is_paid'          => 'boolean',
        'requires_approval' => 'boolean',
        'is_active'        => 'boolean',
    ];

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'policy_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'policy_id');
    }
}
