<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Hr\DepartmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Department (hr.profiles).
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property ?string $parent_department_id
 * @property ?string $head_employee_id
 */
class Department extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_departments';

    protected $fillable = ['company_id', 'name', 'parent_department_id', 'head_employee_id'];

    /** @return HasMany<Employee, $this> */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    /** @return BelongsTo<Employee, $this> */
    public function head(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }
}
