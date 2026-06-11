<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\Employee\EmployeeState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\EmployeeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;
use Spatie\ModelStates\HasStates;

/**
 * The HR anchor record. national_id / date_of_birth / personal_email are
 * encrypted at rest; national_id_hash backs deterministic lookup.
 *
 * @property string $id
 * @property string $company_id
 * @property string|null $user_id
 * @property string $employee_number
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $personal_email
 * @property string|null $date_of_birth
 * @property int|null $birth_year
 * @property string|null $national_id
 * @property string|null $national_id_hash
 * @property Carbon $hire_date
 * @property Carbon|null $termination_date
 * @property string|null $termination_reason
 * @property string $job_title
 * @property string|null $department_id
 * @property string|null $manager_id
 * @property string $employment_type
 * @property EmployeeState $status
 * @property-read string $full_name
 * @property-read Department|null $department
 * @property-read Employee|null $manager
 * @property-read Collection<int, Employee> $directReports
 */
class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, Searchable, SoftDeletes;

    protected $table = 'hr_employees';

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'personal_email',
        'date_of_birth',
        'birth_year',
        'national_id',
        'national_id_hash',
        'hire_date',
        'termination_date',
        'termination_reason',
        'job_title',
        'department_id',
        'manager_id',
        'employment_type',
        'status',
    ];

    /** @var list<string> */
    protected $hidden = ['national_id', 'date_of_birth', 'personal_email'];

    protected function casts(): array
    {
        return [
            'personal_email' => 'encrypted',
            'date_of_birth' => 'encrypted',
            'national_id' => 'encrypted',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'status' => EmployeeState::class,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Scout index — NEVER includes encrypted fields (national_id, DOB,
     * personal_email) per the spec's search rule.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'job_title' => $this->job_title,
        ];
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /** @return BelongsTo<Employee, $this> */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    /** @return HasMany<Employee, $this> */
    public function directReports(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    /** @return HasMany<EmergencyContact, $this> */
    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class, 'employee_id');
    }
}
