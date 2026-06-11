<?php

declare(strict_types=1);

namespace App\Actions\HR;

use App\Data\HR\UpdateOwnProfileData;
use App\Models\HR\EmergencyContact;
use App\Models\HR\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Own-data rule: operates strictly on auth()->user()'s employee record —
 * second isolation layer on top of CompanyScope. Employees may NOT touch
 * name, work email, job, department, manager, salary, national_id.
 */
class UpdateOwnProfileAction
{
    use AsAction;

    public function handle(UpdateOwnProfileData $data): Employee
    {
        $employee = Employee::query()
            ->where('user_id', Auth::guard('web')->id())
            ->first();

        if ($employee === null) {
            throw new AuthorizationException('No employee record is linked to your account.');
        }

        return DB::transaction(function () use ($employee, $data): Employee {
            $employee->update([
                'phone' => $data->phone !== null ? phone($data->phone)->formatE164() : null,
                'personal_email' => $data->personal_email,
            ]);

            // Replace emergency contacts (max 3 enforced by DTO).
            EmergencyContact::query()->where('employee_id', $employee->id)->delete();
            foreach ($data->emergency_contacts as $contact) {
                EmergencyContact::create([
                    'company_id' => $employee->company_id,
                    'employee_id' => $employee->id,
                    'name' => $contact['name'],
                    'relationship' => $contact['relationship'],
                    'phone' => $contact['phone'],
                    'email' => $contact['email'] ?? null,
                ]);
            }

            return $employee->refresh();
        });
    }
}
