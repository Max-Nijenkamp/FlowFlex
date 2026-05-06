<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCustomFieldValue extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'employee_id',
        'custom_field_id',
        'value',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(EmployeeCustomField::class, 'custom_field_id');
    }
}
