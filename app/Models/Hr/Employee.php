<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Enums\Hr\EmploymentStatus;
use App\Enums\Hr\EmploymentType;
use App\Models\File;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Employee extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'date_of_birth',
        'national_id_encrypted',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'department_id',
        'job_title',
        'location',
        'manager_id',
        'start_date',
        'probation_end_date',
        'contracted_hours_per_week',
        'employment_type',
        'employment_status',
        'profile_photo_file_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'        => 'date',
            'start_date'           => 'date',
            'probation_end_date'   => 'date',
            'national_id_encrypted'=> 'encrypted',
            'employment_type'      => EmploymentType::class,
            'employment_status'    => EmploymentStatus::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name', 'last_name', 'email', 'phone',
                'department_id', 'job_title', 'location', 'manager_id',
                'employment_type', 'employment_status', 'start_date',
            ])
            ->logOnlyDirty();
    }

    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function profilePhoto(): BelongsTo
    {
        return $this->belongsTo(File::class, 'profile_photo_file_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(EmployeeCustomFieldValue::class, 'employee_id');
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class, 'employee_id');
    }

    public function onboardingFlows(): HasMany
    {
        return $this->hasMany(OnboardingFlow::class, 'employee_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'employee_id');
    }
}
