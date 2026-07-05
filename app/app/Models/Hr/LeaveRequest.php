<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Models\User;
use App\States\Hr\LeaveRequest\LeaveRequestState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Hr\LeaveRequestFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * Leave request (hr.leave/leave-request-workflow): draft → submitted →
 * approved|rejected, cancellable by the requester; days_requested counts
 * working days.
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $leave_type_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property numeric-string $days_requested
 * @property LeaveRequestState $status
 * @property ?string $note
 * @property ?string $approved_by
 * @property ?Carbon $approved_at
 * @property ?string $rejection_reason
 */
class LeaveRequest extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<LeaveRequestFactory> */
    use HasFactory;

    use HasStates;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_leave_requests';

    protected $fillable = [
        'company_id', 'employee_id', 'leave_type_id', 'start_date', 'end_date',
        'days_requested', 'status', 'note', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_requested' => 'decimal:2',
            'status' => LeaveRequestState::class,
            'approved_at' => 'datetime',
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

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
