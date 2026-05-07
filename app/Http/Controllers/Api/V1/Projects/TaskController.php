<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $tasks = Task::where('company_id', $company->id)
            ->with('assignee')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $tasks->map(fn (Task $t) => [
                'id'               => $t->id,
                'title'            => $t->title,
                'status'           => $t->status?->value ?? $t->status,
                'priority'         => $t->priority?->value ?? $t->priority,
                'assignee_email'   => $t->assignee?->email,
                'due_date'         => $t->due_date?->toDateString(),
                'estimated_hours'  => $t->estimated_hours,
            ]),
            'meta' => [
                'total'        => $tasks->total(),
                'per_page'     => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
                'last_page'    => $tasks->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $task = Task::where('company_id', $company->id)
            ->with('assignee')
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'              => $task->id,
                'title'           => $task->title,
                'description'     => $task->description,
                'status'          => $task->status?->value ?? $task->status,
                'priority'        => $task->priority?->value ?? $task->priority,
                'assignee_email'  => $task->assignee?->email,
                'due_date'        => $task->due_date?->toDateString(),
                'estimated_hours' => $task->estimated_hours,
                'created_at'      => $task->created_at?->toISOString(),
            ],
        ]);
    }
}
