<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\LeaveServiceInterface;
use App\Data\HR\RequestLeaveData;
use App\Events\HR\LeaveApproved;
use App\Events\HR\LeaveRejected;
use App\Events\HR\LeaveRequested;
use App\Models\Company;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaveService implements LeaveServiceInterface
{
    public function requestLeave(RequestLeaveData $data, Company $company): LeaveRequest
    {
        return DB::transaction(function () use ($data, $company): LeaveRequest {
            $balance = LeaveBalance::withoutGlobalScopes()
                ->where('employee_id', $data->employee_id)
                ->where('policy_id', $data->policy_id)
                ->where('year', now()->year)
                ->lockForUpdate()
                ->first();

            if ($balance) {
                $remaining = (float) $balance->allocated_days
                           - (float) $balance->used_days
                           - (float) $balance->pending_days;
                if ($data->days_requested > $remaining) {
                    throw new \RuntimeException('Insufficient leave balance: only ' . $remaining . ' days available.');
                }
            }

            $request = LeaveRequest::withoutGlobalScopes()->create([
                'company_id'     => $company->id,
                'employee_id'    => $data->employee_id,
                'policy_id'      => $data->policy_id,
                'start_date'     => $data->start_date,
                'end_date'       => $data->end_date,
                'days_requested' => $data->days_requested,
                'reason'         => $data->reason,
                'status'         => 'pending',
            ]);

            if ($balance) {
                $balance->increment('pending_days', $data->days_requested);
            }

            event(new LeaveRequested($company, $request));

            return $request;
        });
    }

    public function approve(LeaveRequest $request, User $approver): LeaveRequest
    {
        $request->update([
            'status'      => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        // Move from pending to used
        $balance = LeaveBalance::withoutGlobalScopes()
            ->where('employee_id', $request->employee_id)
            ->where('policy_id', $request->policy_id)
            ->where('year', $request->start_date->year)
            ->first();

        if ($balance) {
            $balance->decrement('pending_days', $request->days_requested);
            $balance->increment('used_days', $request->days_requested);
        }

        $company = $request->company()->withoutGlobalScopes()->first();
        event(new LeaveApproved($company, $request->fresh()));

        return $request->fresh();
    }

    public function reject(LeaveRequest $request, ?string $reason = null): LeaveRequest
    {
        $request->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Remove pending days
        $balance = LeaveBalance::withoutGlobalScopes()
            ->where('employee_id', $request->employee_id)
            ->where('policy_id', $request->policy_id)
            ->where('year', $request->start_date->year)
            ->first();

        if ($balance) {
            $balance->decrement('pending_days', min((float) $request->days_requested, (float) $balance->pending_days));
        }

        $company = $request->company()->withoutGlobalScopes()->first();
        event(new LeaveRejected($company, $request->fresh()));

        return $request->fresh();
    }

    public function cancel(LeaveRequest $request): LeaveRequest
    {
        $previousStatus = $request->status;

        $request->update(['status' => 'cancelled']);

        $balance = LeaveBalance::withoutGlobalScopes()
            ->where('employee_id', $request->employee_id)
            ->where('policy_id', $request->policy_id)
            ->where('year', $request->start_date->year)
            ->first();

        if ($balance) {
            if ($previousStatus === 'pending') {
                $balance->decrement('pending_days', min((float) $request->days_requested, (float) $balance->pending_days));
            } elseif ($previousStatus === 'approved') {
                $balance->decrement('used_days', min((float) $request->days_requested, (float) $balance->used_days));
            }
        }

        $request->delete();

        return $request->fresh() ?? $request;
    }

    public function calculateBalance(string $employeeId, string $policyId, int $year): LeaveBalance
    {
        return LeaveBalance::withoutGlobalScopes()
            ->firstOrCreate(
                [
                    'employee_id' => $employeeId,
                    'policy_id'   => $policyId,
                    'year'        => $year,
                ],
                [
                    'company_id'    => LeaveBalance::withoutGlobalScopes()
                        ->where('employee_id', $employeeId)
                        ->value('company_id'),
                    'allocated_days' => 0,
                    'used_days'     => 0,
                    'pending_days'  => 0,
                ]
            );
    }
}
