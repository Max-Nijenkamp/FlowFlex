<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'year',
        'total_days',
        'used_days',
        'pending_days',
        'carried_over_days',
    ];

    protected function casts(): array
    {
        return [
            'total_days'         => 'decimal:2',
            'used_days'          => 'decimal:2',
            'pending_days'       => 'decimal:2',
            'carried_over_days'  => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function remainingDays(): float
    {
        return (float) $this->total_days - (float) $this->used_days - (float) $this->pending_days;
    }
}
