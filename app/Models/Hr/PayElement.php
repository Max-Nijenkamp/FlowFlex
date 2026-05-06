<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\PayElementType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PayElement extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'payroll_entity_id',
        'name',
        'element_type',
        'is_taxable',
        'is_pensionable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'element_type'   => PayElementType::class,
            'is_taxable'     => 'boolean',
            'is_pensionable' => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'element_type', 'is_taxable', 'is_active'])
            ->logOnlyDirty();
    }

    public function payrollEntity(): BelongsTo
    {
        return $this->belongsTo(PayrollEntity::class, 'payroll_entity_id');
    }
}
