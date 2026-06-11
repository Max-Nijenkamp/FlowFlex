<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->unique()->word().' Leave',
            'accrual_days_per_year' => 25,
            'carry_over_days' => 5,
            'requires_approval' => true,
            'is_paid' => true,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }

    public function unpaid(): static
    {
        return $this->state(fn () => ['is_paid' => false, 'accrual_days_per_year' => 0]);
    }

    public function autoApprove(): static
    {
        return $this->state(fn () => ['requires_approval' => false]);
    }
}
