<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'leave_balances';

    protected $fillable = [
        'company_id',
        'employee_id',
        'policy_id',
        'year',
        'allocated_days',
        'used_days',
        'pending_days',
    ];

    protected $casts = [
        'year'          => 'integer',
        'allocated_days' => 'decimal:1',
        'used_days'     => 'decimal:1',
        'pending_days'  => 'decimal:1',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(LeavePolicy::class, 'policy_id');
    }

    public function getRemainingDaysAttribute(): float
    {
        return (float) $this->allocated_days - (float) $this->used_days - (float) $this->pending_days;
    }
}
