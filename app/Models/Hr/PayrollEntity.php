<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PayrollEntity extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'legal_name',
        'country_code',
        'tax_reference_encrypted',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default'              => 'boolean',
            'tax_reference_encrypted' => 'encrypted',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        // tax_reference_encrypted is intentionally excluded — encrypted sensitive fields must not be logged.
        return LogOptions::defaults()
            ->logOnly(['name', 'legal_name', 'country_code', 'is_default'])
            ->logOnlyDirty();
    }

    public function payElements(): HasMany
    {
        return $this->hasMany(PayElement::class, 'payroll_entity_id');
    }

    public function payRuns(): HasMany
    {
        return $this->hasMany(PayRun::class, 'payroll_entity_id');
    }

    public function taxConfigurations(): HasMany
    {
        return $this->hasMany(TaxConfiguration::class, 'payroll_entity_id');
    }
}
