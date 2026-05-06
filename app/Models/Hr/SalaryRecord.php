<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\PayFrequency;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SalaryRecord extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_id',
        'salary_encrypted',
        'currency',
        'pay_frequency',
        'effective_from',
        'effective_to',
        'notes',
        'created_by_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'salary_encrypted' => 'encrypted',
            'pay_frequency'    => PayFrequency::class,
            'effective_from'   => 'date',
            'effective_to'     => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['currency', 'pay_frequency', 'effective_from', 'effective_to'])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'created_by_tenant_id');
    }
}
