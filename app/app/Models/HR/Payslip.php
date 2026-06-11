<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * amounts_raw = encrypted JSON {gross_cents, net_cents, employer_cost_cents,
 * deductions: [{name, amount_cents}]}. Kept 7 years per data-lifecycle.
 *
 * @property string $id
 * @property string $company_id
 * @property string $payroll_run_id
 * @property string $employee_id
 * @property string $amounts_raw
 * @property string|null $pdf_path
 * @property-read Employee $employee
 */
class Payslip extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_payslips';

    protected $fillable = ['company_id', 'payroll_run_id', 'employee_id', 'amounts_raw', 'pdf_path'];

    /** @var list<string> */
    protected $hidden = ['amounts_raw'];

    protected function casts(): array
    {
        return ['amounts_raw' => 'encrypted'];
    }

    /** @return array<string, mixed> decrypted amounts — caller must hold view-sensitive */
    public function amounts(): array
    {
        return json_decode($this->amounts_raw, true, 512, JSON_THROW_ON_ERROR);
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
