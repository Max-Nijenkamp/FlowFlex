<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Exceptions\HR\EmptyCycleException;
use App\Exceptions\HR\ReviewLockedException;
use App\Models\HR\Employee;
use App\Models\HR\Review;
use App\Models\HR\ReviewCycle;
use App\States\HR\ReviewCycle\Active;
use App\States\HR\ReviewCycle\Calibration;
use App\States\HR\ReviewCycle\Finalised;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /** Generates the review matrix: self + manager review per active employee. */
    public function activateCycle(string $cycleId): ReviewCycle
    {
        $cycle = ReviewCycle::query()->findOrFail($cycleId);
        $employees = Employee::query()->where('status', 'active')->get();

        if ($employees->isEmpty()) {
            throw new EmptyCycleException('No active employees for this review cycle.');
        }

        return DB::transaction(function () use ($cycle, $employees): ReviewCycle {
            foreach ($employees as $employee) {
                Review::query()->firstOrCreate([
                    'cycle_id' => $cycle->id,
                    'employee_id' => $employee->id,
                    'reviewer_id' => null,
                    'type' => 'self',
                ], ['company_id' => $cycle->company_id]);

                if ($employee->manager_id !== null) {
                    Review::query()->firstOrCreate([
                        'cycle_id' => $cycle->id,
                        'employee_id' => $employee->id,
                        'reviewer_id' => $employee->manager_id,
                        'type' => 'manager',
                    ], ['company_id' => $cycle->company_id]);
                }
            }

            $cycle->status->transitionTo(Active::class);

            return $cycle->refresh();
        });
    }

    /** @param array<string, mixed> $content */
    public function submitReview(string $reviewId, array $content, ?float $rating = null): Review
    {
        $review = Review::query()->with('cycle')->findOrFail($reviewId);

        if (! ReviewCycle::query()->whereKey($review->cycle_id)->where('status', 'active')->exists()) {
            throw new ReviewLockedException('Reviews can only be submitted while the cycle is active.');
        }

        $review->update([
            'content' => $content,
            'rating' => $rating,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $review->refresh();
    }

    public function startCalibration(string $cycleId): ReviewCycle
    {
        $cycle = ReviewCycle::query()->findOrFail($cycleId);
        $cycle->status->transitionTo(Calibration::class);

        return $cycle->refresh();
    }

    /** Calibration-state only; audited via activitylog (state transitions). */
    public function calibrate(string $reviewId, float $rating): Review
    {
        $review = Review::query()->findOrFail($reviewId);

        if (! ReviewCycle::query()->whereKey($review->cycle_id)->where('status', 'calibration')->exists()) {
            throw new ReviewLockedException('Calibration is only allowed in the calibration state.');
        }

        $review->update(['rating' => $rating]);

        return $review->refresh();
    }

    public function finalise(string $cycleId): ReviewCycle
    {
        $cycle = ReviewCycle::query()->findOrFail($cycleId);
        $cycle->status->transitionTo(Finalised::class);

        return $cycle->refresh();
    }
}
