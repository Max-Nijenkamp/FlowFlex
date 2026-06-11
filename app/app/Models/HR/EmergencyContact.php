<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * Hard-deleted on employee GDPR erasure (architecture/data-lifecycle).
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $name
 * @property string $relationship
 * @property string $phone
 * @property string|null $email
 */
class EmergencyContact extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_emergency_contacts';

    protected $fillable = ['company_id', 'employee_id', 'name', 'relationship', 'phone', 'email'];
}
