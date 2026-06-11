<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingTemplate>
 */
class OnboardingTemplateFactory extends Factory
{
    protected $model = OnboardingTemplate::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Default Onboarding',
            'is_default' => true,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }
}
