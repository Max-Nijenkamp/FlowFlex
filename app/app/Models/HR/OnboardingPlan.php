<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\OnboardingPlanFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $template_id
 * @property Carbon $started_at
 * @property Carbon|null $completed_at
 * @property-read Employee $employee
 * @property-read Collection<int, OnboardingPlanTask> $tasks
 */
class OnboardingPlan extends Model
{
    /** @use HasFactory<OnboardingPlanFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_onboarding_plans';

    protected $fillable = ['company_id', 'employee_id', 'template_id', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** @return HasMany<OnboardingPlanTask, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingPlanTask::class, 'plan_id');
    }
}
