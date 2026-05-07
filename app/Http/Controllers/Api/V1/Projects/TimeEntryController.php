<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $timeEntries = TimeEntry::where('company_id', $company->id)
            ->orderBy('entry_date', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $timeEntries->map(fn (TimeEntry $te) => [
                'id'          => $te->id,
                'task_id'     => $te->task_id,
                'entry_date'  => $te->entry_date?->toDateString(),
                'minutes'     => $te->minutes,
                'description' => $te->description,
                'is_billable' => $te->is_billable,
                'is_approved' => $te->is_approved,
            ]),
            'meta' => [
                'total'        => $timeEntries->total(),
                'per_page'     => $timeEntries->perPage(),
                'current_page' => $timeEntries->currentPage(),
                'last_page'    => $timeEntries->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $timeEntry = TimeEntry::where('company_id', $company->id)
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'          => $timeEntry->id,
                'task_id'     => $timeEntry->task_id,
                'entry_date'  => $timeEntry->entry_date?->toDateString(),
                'minutes'     => $timeEntry->minutes,
                'description' => $timeEntry->description,
                'is_billable' => $timeEntry->is_billable,
                'is_approved' => $timeEntry->is_approved,
            ],
        ]);
    }
}
