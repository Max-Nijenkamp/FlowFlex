<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\HR\OnboardingTemplateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string|null $description
 * @property string|null $department_id
 * @property bool $is_default
 */
class OnboardingTemplate extends Model
{
    /** @use HasFactory<OnboardingTemplateFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_onboarding_templates';

    protected $fillable = ['company_id', 'name', 'description', 'department_id', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    /** @return HasMany<OnboardingTask, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTask::class, 'template_id');
    }
}
