<?php

namespace App\Http\Controllers\Api\V1\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $leaveRequests = LeaveRequest::where('company_id', $company->id)
            ->with(['employee', 'leaveType'])
            ->orderBy('start_date', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $leaveRequests->map(fn (LeaveRequest $lr) => [
                'id'            => $lr->id,
                'employee_name' => $lr->employee
                    ? "{$lr->employee->first_name} {$lr->employee->last_name}"
                    : null,
                'leave_type'    => $lr->leaveType?->name,
                'start_date'    => $lr->start_date?->toDateString(),
                'end_date'      => $lr->end_date?->toDateString(),
                'total_days'    => $lr->total_days,
                'status'        => $lr->status?->value ?? $lr->status,
            ]),
            'meta' => [
                'total'        => $leaveRequests->total(),
                'per_page'     => $leaveRequests->perPage(),
                'current_page' => $leaveRequests->currentPage(),
                'last_page'    => $leaveRequests->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $leaveRequest = LeaveRequest::where('company_id', $company->id)
            ->with(['employee', 'leaveType'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'               => $leaveRequest->id,
                'employee_name'    => $leaveRequest->employee
                    ? "{$leaveRequest->employee->first_name} {$leaveRequest->employee->last_name}"
                    : null,
                'leave_type'       => $leaveRequest->leaveType?->name,
                'start_date'       => $leaveRequest->start_date?->toDateString(),
                'end_date'         => $leaveRequest->end_date?->toDateString(),
                'total_days'       => $leaveRequest->total_days,
                'status'           => $leaveRequest->status?->value ?? $leaveRequest->status,
                'reason'           => $leaveRequest->reason,
                'rejection_reason' => $leaveRequest->rejection_reason,
            ],
        ]);
    }
}
