<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function index(): JsonResponse
    {
        $tasks = Task::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id'     => ['required', 'string'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'status'         => ['nullable', 'string'],
            'priority'       => ['nullable', 'string'],
            'assignee_id'    => ['nullable', 'string'],
            'due_date'       => ['nullable', 'date'],
            'start_date'     => ['nullable', 'date'],
            'estimate_hours' => ['nullable', 'numeric'],
            'story_points'   => ['nullable', 'integer'],
            'parent_id'      => ['nullable', 'string'],
        ]);

        $data['company_id'] = $this->companyContext->currentId();
        $data['created_by'] = $request->user()->id;

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show(string $id): JsonResponse
    {
        $task = Task::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        return response()->json($task);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $data = $request->validate([
            'title'          => ['sometimes', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'status'         => ['nullable', 'string'],
            'priority'       => ['nullable', 'string'],
            'assignee_id'    => ['nullable', 'string'],
            'due_date'       => ['nullable', 'date'],
            'start_date'     => ['nullable', 'date'],
            'estimate_hours' => ['nullable', 'numeric'],
            'story_points'   => ['nullable', 'integer'],
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function destroy(string $id): JsonResponse
    {
        $task = Task::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $task->delete();

        return response()->json(null, 204);
    }
}
