<?php

namespace App\Http\Controllers\Api\V1\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $employees = Employee::where('company_id', $company->id)
            ->with('department')
            ->orderBy('last_name')
            ->paginate(25);

        return response()->json([
            'data' => $employees->map(fn (Employee $e) => [
                'id'                => $e->id,
                'employee_number'   => $e->employee_number,
                'first_name'        => $e->first_name,
                'last_name'         => $e->last_name,
                'email'             => $e->email,
                'job_title'         => $e->job_title,
                'department'        => $e->department?->name,
                'employment_status' => $e->employment_status?->value,
                'employment_type'   => $e->employment_type?->value,
                'start_date'        => $e->start_date?->toDateString(),
            ]),
            'meta' => [
                'total'        => $employees->total(),
                'per_page'     => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'last_page'    => $employees->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $employee = Employee::where('company_id', $company->id)
            ->with(['department', 'manager'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'                        => $employee->id,
                'employee_number'           => $employee->employee_number,
                'first_name'                => $employee->first_name,
                'last_name'                 => $employee->last_name,
                'email'                     => $employee->email,
                'phone'                     => $employee->phone,
                'job_title'                 => $employee->job_title,
                'department'                => $employee->department?->name,
                'manager'                   => $employee->manager
                    ? "{$employee->manager->first_name} {$employee->manager->last_name}"
                    : null,
                'employment_status'         => $employee->employment_status?->value,
                'employment_type'           => $employee->employment_type?->value,
                'start_date'                => $employee->start_date?->toDateString(),
                'contracted_hours_per_week' => $employee->contracted_hours_per_week,
            ],
        ]);
    }
}
