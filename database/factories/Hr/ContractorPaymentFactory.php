<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\ContractorPayment;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractorPayment>
 */
class ContractorPaymentFactory extends Factory
{
    protected $model = ContractorPayment::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'employee_id' => Employee::factory(),
            'amount'      => $this->faker->randomFloat(2, 500, 10000),
            'currency'    => 'EUR',
            'reference'   => 'REF-' . $this->faker->unique()->numerify('####'),
            'status'      => 'pending',
        ];
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => 'processed',
            'processed_at' => now(),
        ]);
    }
}
