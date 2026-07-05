<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Models\User;
use App\States\Hr\Employee\EmployeeState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Hr\EmployeeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStates\HasStates;

/**
 * Canonical employee record — the HR anchor (hr.profiles). national_id,
 * date_of_birth and personal_email are encrypted at rest; the hash and
 * birth_year columns exist for lookups the ciphertext can't serve.
 * Documents + profile photo ride the media library (tenant paths via
 * CompanyPathGenerator).
 *
 * @property string $id
 * @property string $company_id
 * @property ?string $user_id
 * @property string $employee_number
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property ?string $phone
 * @property ?string $personal_email
 * @property ?string $date_of_birth
 * @property ?int $birth_year
 * @property ?string $national_id
 * @property ?string $national_id_hash
 * @property Carbon $hire_date
 * @property ?Carbon $termination_date
 * @property ?string $termination_reason
 * @property string $job_title
 * @property ?string $department_id
 * @property ?string $manager_id
 * @property string $employment_type
 * @property EmployeeState $status
 * @property-read string $full_name
 */
class Employee extends Model implements HasMedia
{
    use BelongsToCompany;

    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    use HasStates;
    use HasUlids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'hr_employees';

    protected $fillable = [
        'company_id', 'user_id', 'employee_number', 'first_name', 'last_name',
        'email', 'phone', 'personal_email', 'date_of_birth', 'birth_year',
        'national_id', 'national_id_hash', 'hire_date', 'termination_date',
        'termination_reason', 'job_title', 'department_id', 'manager_id',
        'employment_type', 'status',
    ];

    protected $hidden = ['national_id', 'date_of_birth', 'personal_email'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'personal_email' => 'encrypted',
            'date_of_birth' => 'encrypted',
            'national_id' => 'encrypted',
            'birth_year' => 'integer',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'status' => EmployeeState::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
        $this->addMediaCollection('documents');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
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

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
