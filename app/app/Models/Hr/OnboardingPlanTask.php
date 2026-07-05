<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Materialised task on a plan (hr.onboarding/task-checklists).
 *
 * @property string $id
 * @property string $company_id
 * @property string $plan_id
 * @property string $task_id
 * @property string $status pending | complete | skipped
 * @property ?string $completed_by
 * @property ?Carbon $completed_at
 */
class OnboardingPlanTask extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_onboarding_plan_tasks';

    protected $fillable = ['company_id', 'plan_id', 'task_id', 'status', 'completed_by', 'completed_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    /** @return BelongsTo<OnboardingPlan, $this> */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(OnboardingPlan::class, 'plan_id');
    }

    /** @return BelongsTo<OnboardingTask, $this> */
    public function task(): BelongsTo
    {
        return $this->belongsTo(OnboardingTask::class, 'task_id');
    }

    /** @return BelongsTo<User, $this> */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
