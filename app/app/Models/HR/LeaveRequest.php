<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\LeaveRequest\LeaveRequestState;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Database\Factories\HR\LeaveRequestFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $leave_type_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property float $days_requested
 * @property LeaveRequestState $status
 * @property string|null $note
 * @property string|null $approved_by
 * @property Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property-read Employee $employee
 * @property-read LeaveType $leaveType
 */
class LeaveRequest extends Model
{
    /** @use HasFactory<LeaveRequestFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'hr_leave_requests';

    protected $fillable = [
        'company_id', 'employee_id', 'leave_type_id', 'start_date', 'end_date',
        'days_requested', 'status', 'note', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_requested' => 'float',
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
}
