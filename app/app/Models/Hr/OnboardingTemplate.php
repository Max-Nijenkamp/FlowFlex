<?php

declare(strict_types=1);

namespace App\Models\Hr;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Hr\OnboardingTemplateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Onboarding template (hr.onboarding/onboarding-templates). Department-
 * specific templates win over the company default at plan generation.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property ?string $description
 * @property ?string $department_id
 * @property bool $is_default
 */
class OnboardingTemplate extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<OnboardingTemplateFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'hr_onboarding_templates';

    protected $fillable = ['company_id', 'name', 'description', 'department_id', 'is_default'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    /** @return HasMany<OnboardingTask, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTask::class, 'template_id');
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
