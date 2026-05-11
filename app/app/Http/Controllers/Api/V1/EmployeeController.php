<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Support\Services\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function index(): JsonResponse
    {
        $employees = Employee::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->get();

        return response()->json($employees);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'       => ['required', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'employee_number'  => ['nullable', 'string', 'max:50'],
            'date_of_birth'    => ['nullable', 'date'],
            'hire_date'        => ['nullable', 'date'],
            'employment_type'  => ['nullable', 'string'],
            'department'       => ['nullable', 'string', 'max:255'],
            'job_title'        => ['nullable', 'string', 'max:255'],
            'manager_id'       => ['nullable', 'string'],
            'location'         => ['nullable', 'string', 'max:255'],
            'status'           => ['nullable', 'string'],
        ]);

        $data['company_id'] = $this->companyContext->currentId();

        $employee = Employee::create($data);

        return response()->json($employee, 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = Employee::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        return response()->json($employee);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $data = $request->validate([
            'first_name'       => ['sometimes', 'string', 'max:255'],
            'last_name'        => ['sometimes', 'string', 'max:255'],
            'email'            => ['sometimes', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'employee_number'  => ['nullable', 'string', 'max:50'],
            'date_of_birth'    => ['nullable', 'date'],
            'hire_date'        => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'employment_type'  => ['nullable', 'string'],
            'department'       => ['nullable', 'string', 'max:255'],
            'job_title'        => ['nullable', 'string', 'max:255'],
            'manager_id'       => ['nullable', 'string'],
            'location'         => ['nullable', 'string', 'max:255'],
            'status'           => ['nullable', 'string'],
        ]);

        $employee->update($data);

        return response()->json($employee);
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $employee->delete();

        return response()->json(null, 204);
    }
}
