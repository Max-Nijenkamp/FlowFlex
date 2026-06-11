<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeek()->addDays(4),
            'days_requested' => 5,
        ];
    }
}
