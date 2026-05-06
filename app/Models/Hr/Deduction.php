<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Deduction extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_id',
        'pay_element_id',
        'name',
        'deduction_type',
        'amount',
        'is_percentage',
        'is_recurring',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'decimal:2',
            'is_percentage'  => 'boolean',
            'is_recurring'   => 'boolean',
            'effective_from' => 'date',
            'effective_to'   => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'deduction_type', 'amount', 'is_recurring', 'effective_from', 'effective_to'])
            ->logOnlyDirty();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payElement(): BelongsTo
    {
        return $this->belongsTo(PayElement::class, 'pay_element_id');
    }
}
