<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\PayrollRun\PayrollRunState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\PayrollRunFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property PayrollRunState $status
 * @property int $total_gross_cents
 * @property int $total_net_cents
 * @property int $total_employer_cost_cents
 * @property string $currency
 * @property string|null $created_by
 * @property string|null $approved_by
 * @property Carbon|null $approved_at
 * @property-read Collection<int, Payslip> $payslips
 */
class PayrollRun extends Model
{
    /** @use HasFactory<PayrollRunFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $table = 'hr_payroll_runs';

    protected $fillable = [
        'company_id', 'period_start', 'period_end', 'status', 'total_gross_cents',
        'total_net_cents', 'total_employer_cost_cents', 'currency', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'status' => PayrollRunState::class,
            'total_gross_cents' => 'integer',
            'total_net_cents' => 'integer',
            'total_employer_cost_cents' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    /** @return HasMany<Payslip, $this> */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'payroll_run_id');
    }
}
