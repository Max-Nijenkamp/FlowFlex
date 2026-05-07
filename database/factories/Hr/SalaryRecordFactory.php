<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\PayFrequency;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\SalaryRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalaryRecord>
 */
class SalaryRecordFactory extends Factory
{
    protected $model = SalaryRecord::class;

    public function definition(): array
    {
        return [
            'company_id'    => Company::factory(),
            'employee_id'   => Employee::factory(),
            'currency'      => 'EUR',
            'pay_frequency' => PayFrequency::Monthly->value,
            'effective_from'=> now()->startOfYear()->format('Y-m-d'),
        ];
    }
}
