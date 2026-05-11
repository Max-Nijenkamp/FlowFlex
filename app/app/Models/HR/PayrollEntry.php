<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEntry extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'payroll_entries';

    protected $fillable = [
        'company_id',
        'run_id',
        'employee_id',
        'gross_pay',
        'net_pay',
        'deductions',
        'additions',
        'notes',
    ];

    protected $casts = [
        'gross_pay'  => 'decimal:2',
        'net_pay'    => 'decimal:2',
        'deductions' => 'array',
        'additions'  => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getTotalDeductionsAttribute(): float
    {
        if (empty($this->deductions)) {
            return 0.0;
        }

        return array_sum(array_column($this->deductions, 'amount'));
    }

    public function getTotalAdditionsAttribute(): float
    {
        if (empty($this->additions)) {
            return 0.0;
        }

        return array_sum(array_column($this->additions, 'amount'));
    }
}
