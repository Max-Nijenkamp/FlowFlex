<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Per-employee per-type per-year balance (hr.leave/leave-balances).
 * pending_days moves on submit/decide, taken_days on approval.
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $leave_type_id
 * @property int $year
 * @property numeric-string $allocated_days
 * @property numeric-string $taken_days
 * @property numeric-string $pending_days
 */
class LeaveBalance extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'company_id', 'employee_id', 'leave_type_id', 'year',
        'allocated_days', 'taken_days', 'pending_days',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'allocated_days' => 'decimal:2',
            'taken_days' => 'decimal:2',
            'pending_days' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** @return BelongsTo<LeaveType, $this> */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function remainingDays(): float
    {
        return (float) $this->allocated_days - (float) $this->taken_days - (float) $this->pending_days;
    }
}
