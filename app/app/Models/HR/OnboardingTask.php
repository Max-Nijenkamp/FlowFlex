<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * Template line — append-only beside its template.
 *
 * @property string $id
 * @property string $template_id
 * @property string $company_id
 * @property string $title
 * @property string|null $description
 * @property string $assigned_role
 * @property int|null $due_days_after_start
 * @property int $order
 */
class OnboardingTask extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_onboarding_tasks';

    protected $fillable = ['template_id', 'company_id', 'title', 'description', 'assigned_role', 'due_days_after_start', 'order'];
}
