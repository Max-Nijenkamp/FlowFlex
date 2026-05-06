<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayRunEmployee extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'pay_run_id',
        'employee_id',
        'gross_pay',
        'net_pay',
        'total_deductions',
        'adjustments',
    ];

    protected function casts(): array
    {
        return [
            'gross_pay'        => 'decimal:2',
            'net_pay'          => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'adjustments'      => 'array',
        ];
    }

    public function payRun(): BelongsTo
    {
        return $this->belongsTo(PayRun::class, 'pay_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayRunLine::class, 'pay_run_employee_id');
    }
}
