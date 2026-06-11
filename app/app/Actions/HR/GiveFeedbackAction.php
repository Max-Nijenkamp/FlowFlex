<?php

declare(strict_types=1);

namespace App\Actions\HR;

use App\Models\HR\Feedback;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class GiveFeedbackAction
{
    use AsAction;

    public function handle(string $fromEmployeeId, string $toEmployeeId, string $type, string $message): Feedback
    {
        if ($fromEmployeeId === $toEmployeeId) {
            throw ValidationException::withMessages(['to_employee_id' => 'You cannot give feedback to yourself.']);
        }

        // Visibility forced by type (spec rule).
        $visibility = match ($type) {
            'praise' => 'public',
            'constructive' => 'private',
            'coaching-note' => 'manager-chain',
            default => throw ValidationException::withMessages(['type' => 'Unknown feedback type.']),
        };

        return Feedback::create([
            'from_employee_id' => $fromEmployeeId,
            'to_employee_id' => $toEmployeeId,
            'type' => $type,
            'message' => $message,
            'visibility' => $visibility,
        ]);
    }
}
