<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Hr\LeaveTypeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Leave type (hr.leave/leave-types): accrual, carry-over cap, approval
 * requirement and calendar colour per company.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $color
 * @property numeric-string $accrual_days_per_year
 * @property int $carry_over_days
 * @property bool $requires_approval
 */
class LeaveType extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<LeaveTypeFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_leave_types';

    protected $fillable = [
        'company_id', 'name', 'color', 'accrual_days_per_year',
        'carry_over_days', 'requires_approval',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'accrual_days_per_year' => 'decimal:2',
            'carry_over_days' => 'integer',
            'requires_approval' => 'boolean',
        ];
    }

    /** @return HasMany<LeaveRequest, $this> */
    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }
}
