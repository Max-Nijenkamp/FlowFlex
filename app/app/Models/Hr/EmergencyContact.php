<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Emergency contact (hr.profiles). Hard-deleted on employee erasure
 * per data-lifecycle.
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $name
 * @property string $relationship
 * @property string $phone
 * @property ?string $email
 */
class EmergencyContact extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_emergency_contacts';

    protected $fillable = ['company_id', 'employee_id', 'name', 'relationship', 'phone', 'email'];

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
