<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A running onboarding (hr.onboarding). completed_at stamps when the
 * last task is done or skipped.
 *
 * @property string $id
 * @property string $company_id
 * @property string $employee_id
 * @property string $template_id
 * @property Carbon $started_at
 * @property ?Carbon $completed_at
 */
class OnboardingPlan extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_onboarding_plans';

    protected $fillable = ['company_id', 'employee_id', 'template_id', 'started_at', 'completed_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** @return BelongsTo<OnboardingTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    /** @return HasMany<OnboardingPlanTask, $this> */
    public function planTasks(): HasMany
    {
        return $this->hasMany(OnboardingPlanTask::class, 'plan_id');
    }

    public function progressPercent(): int
    {
        $total = $this->planTasks()->count();

        if ($total === 0) {
            return 100;
        }

        $done = $this->planTasks()->whereIn('status', ['complete', 'skipped'])->count();

        return (int) round($done / $total * 100);
    }
}
