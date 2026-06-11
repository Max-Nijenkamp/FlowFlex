<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\PayrollEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollEmployee>
 */
class PayrollEmployeeFactory extends Factory
{
    protected $model = PayrollEmployee::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'salary_raw' => '350000', // €3,500.00 monthly gross
            'pay_type' => 'salaried',
            'status' => 'ready',
        ];
    }
}
