<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Template task (hr.onboarding). assigned_role routes the work: hr /
 * it (equipment requests) / manager / employee (self-service);
 * due_days_after_start drives milestone check-ins (30/60/90).
 *
 * @property string $id
 * @property string $company_id
 * @property string $template_id
 * @property string $title
 * @property ?string $description
 * @property string $assigned_role
 * @property ?int $due_days_after_start
 * @property int $order
 */
class OnboardingTask extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    public const ROLES = ['hr', 'it', 'manager', 'employee'];

    protected $table = 'hr_onboarding_tasks';

    protected $fillable = [
        'company_id', 'template_id', 'title', 'description',
        'assigned_role', 'due_days_after_start', 'order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['due_days_after_start' => 'integer', 'order' => 'integer'];
    }

    /** @return BelongsTo<OnboardingTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }
}
