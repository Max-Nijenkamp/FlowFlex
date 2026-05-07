<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class PayRunLine extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'pay_run_employee_id',
        'pay_element_id',
        'description',
        'amount',
        'is_deduction',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'is_deduction' => 'boolean',
        ];
    }

    public function payRunEmployee(): BelongsTo
    {
        return $this->belongsTo(PayRunEmployee::class, 'pay_run_employee_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function payElement(): BelongsTo
    {
        return $this->belongsTo(PayElement::class, 'pay_element_id');
    }
}
