<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Support\Services\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function index(): JsonResponse
    {
        $projects = Project::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->get();

        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'string'],
            'priority'    => ['nullable', 'string'],
            'start_date'  => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date'],
            'budget'      => ['nullable', 'numeric'],
            'color'       => ['nullable', 'string', 'max:20'],
        ]);

        $data['company_id'] = $this->companyContext->currentId();
        $data['owner_id']   = $request->user()->id;

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function show(string $id): JsonResponse
    {
        $project = Project::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        return response()->json($project);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $project = Project::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'string'],
            'priority'    => ['nullable', 'string'],
            'start_date'  => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date'],
            'budget'      => ['nullable', 'numeric'],
            'color'       => ['nullable', 'string', 'max:20'],
        ]);

        $project->update($data);

        return response()->json($project);
    }

    public function destroy(string $id): JsonResponse
    {
        $project = Project::withoutGlobalScopes()
            ->where('company_id', $this->companyContext->currentId())
            ->findOrFail($id);

        $project->delete();

        return response()->json(null, 204);
    }
}
