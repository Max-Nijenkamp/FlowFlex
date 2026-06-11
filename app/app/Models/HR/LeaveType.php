<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\LeaveTypeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $color
 * @property float $accrual_days_per_year
 * @property int $carry_over_days
 * @property bool $requires_approval
 * @property bool $is_paid
 */
class LeaveType extends Model
{
    /** @use HasFactory<LeaveTypeFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_leave_types';

    protected $fillable = ['company_id', 'name', 'color', 'accrual_days_per_year', 'carry_over_days', 'requires_approval', 'is_paid'];

    protected function casts(): array
    {
        return [
            'accrual_days_per_year' => 'float',
            'carry_over_days' => 'integer',
            'requires_approval' => 'boolean',
            'is_paid' => 'boolean',
        ];
    }
}
