<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\LeaveBalanceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $leave_type_id
 * @property int $year
 * @property float $allocated_days
 * @property float $taken_days
 * @property float $pending_days
 * @property-read float $remaining_days
 */
class LeaveBalance extends Model
{
    /** @use HasFactory<LeaveBalanceFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_leave_balances';

    protected $fillable = ['company_id', 'employee_id', 'leave_type_id', 'year', 'allocated_days', 'taken_days', 'pending_days'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'allocated_days' => 'float',
            'taken_days' => 'float',
            'pending_days' => 'float',
        ];
    }

    public function getRemainingDaysAttribute(): float
    {
        return $this->allocated_days - $this->taken_days - $this->pending_days;
    }
}
