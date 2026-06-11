<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\HR\LeaveServiceInterface;
use App\Data\HR\LeaveRequestData;
use App\Data\HR\SubmitLeaveRequestData;
use App\Http\Controllers\Controller;
use App\Models\HR\LeaveRequest;
use Illuminate\Http\JsonResponse;

class LeaveRequestController extends Controller
{
    public function __construct(
        private readonly LeaveServiceInterface $leave,
    ) {}

    public function index(): JsonResponse
    {
        // Cursor pagination — append-only feed per api-design pagination rule.
        $page = LeaveRequest::query()->orderByDesc('id')->cursorPaginate(min((int) request('per_page', 25), 100));

        return response()->json([
            'data' => collect($page->items())->map(fn (LeaveRequest $r) => LeaveRequestData::fromModel($r)),
            'meta' => ['next_cursor' => $page->nextCursor()?->encode(), 'per_page' => $page->perPage()],
        ]);
    }

    public function store(SubmitLeaveRequestData $data): JsonResponse
    {
        return response()->json(['data' => LeaveRequestData::fromModel($this->leave->submit($data))], 201);
    }

    public function approve(string $id): JsonResponse
    {
        return response()->json(['data' => LeaveRequestData::fromModel($this->leave->approve($id))]);
    }

    public function reject(string $id): JsonResponse
    {
        return response()->json(['data' => LeaveRequestData::fromModel(
            $this->leave->reject($id, (string) request('rejection_reason', ''))
        )]);
    }
}
