<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\PayElementType;
use App\Models\Company;
use App\Models\Hr\PayElement;
use App\Models\Hr\PayrollEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayElement>
 */
class PayElementFactory extends Factory
{
    protected $model = PayElement::class;

    public function definition(): array
    {
        return [
            'company_id'         => Company::factory(),
            'payroll_entity_id'  => PayrollEntity::factory(),
            'name'               => $this->faker->randomElement(['Basic Salary', 'Overtime', 'Bonus', 'Pension Deduction', 'Health Insurance']),
            'element_type'       => $this->faker->randomElement(PayElementType::cases())->value,
            'is_taxable'         => true,
            'is_pensionable'     => true,
            'is_active'          => true,
        ];
    }
}
