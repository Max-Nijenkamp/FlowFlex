<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\CustomFieldType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class EmployeeCustomField extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'key',
        'field_type',
        'options',
        'is_required',
        'is_visible_to_employee',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'field_type'              => CustomFieldType::class,
            'options'                 => 'array',
            'is_required'             => 'boolean',
            'is_visible_to_employee'  => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'key', 'field_type', 'is_required', 'sort_order'])
            ->logOnlyDirty();
    }

    public function values(): HasMany
    {
        return $this->hasMany(EmployeeCustomFieldValue::class, 'custom_field_id');
    }
}
