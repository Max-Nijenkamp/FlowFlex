<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\PayrollEmployeeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Per-employee payroll profile (created by the EmployeeHired listener).
 * salary_raw / iban / hourly_rate_raw encrypted at rest.
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string|null $salary_raw
 * @property string|null $salary_band
 * @property string|null $iban
 * @property string $pay_type
 * @property string|null $hourly_rate_raw
 * @property string $status
 * @property bool $final_pay_flagged
 * @property-read Employee $employee
 */
class PayrollEmployee extends Model
{
    /** @use HasFactory<PayrollEmployeeFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_payroll_employees';

    protected $fillable = [
        'company_id', 'employee_id', 'salary_raw', 'salary_band', 'iban',
        'pay_type', 'hourly_rate_raw', 'status', 'final_pay_flagged',
    ];

    /** @var list<string> */
    protected $hidden = ['salary_raw', 'iban', 'hourly_rate_raw'];

    protected function casts(): array
    {
        return [
            'salary_raw' => 'encrypted',
            'iban' => 'encrypted',
            'hourly_rate_raw' => 'encrypted',
            'final_pay_flagged' => 'boolean',
        ];
    }

    public function salaryCents(): ?int
    {
        return $this->salary_raw !== null ? (int) $this->salary_raw : null;
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
