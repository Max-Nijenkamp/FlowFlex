<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $plan_id
 * @property string $task_id
 * @property string $company_id
 * @property string $status
 * @property string|null $completed_by
 * @property Carbon|null $completed_at
 * @property-read OnboardingTask $task
 */
class OnboardingPlanTask extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_onboarding_plan_tasks';

    protected $fillable = ['plan_id', 'task_id', 'company_id', 'status', 'completed_by', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    /** @return BelongsTo<OnboardingTask, $this> */
    public function task(): BelongsTo
    {
        return $this->belongsTo(OnboardingTask::class, 'task_id');
    }
}
