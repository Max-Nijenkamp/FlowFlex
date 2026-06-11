<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\EmployeeData;
use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeServiceInterface $employees,
    ) {}

    public function index(): JsonResponse
    {
        $page = Employee::query()->latest('hire_date')->paginate(min((int) request('per_page', 25), 100));

        return response()->json([
            'data' => collect($page->items())->map(fn (Employee $e) => EmployeeData::fromModel($e)),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()],
        ]);
    }

    public function store(CreateEmployeeData $data): JsonResponse
    {
        return response()->json(['data' => EmployeeData::fromModel($this->employees->hire($data))], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['data' => EmployeeData::fromModel(Employee::query()->findOrFail($id))]);
    }
}
