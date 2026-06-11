<?php

declare(strict_types=1);

namespace App\Actions\HR;

use App\Models\ConsentLog;
use App\Models\HR\DeiAttribute;
use App\Models\HR\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmitOwnDeiAttributesAction
{
    use AsAction;

    /** Own-only; writes a consent log entry (core.privacy). @param array<string, string> $attributes dimension => value */
    public function handle(array $attributes): void
    {
        $employee = Employee::query()->where('user_id', Auth::guard('web')->id())->first();

        if ($employee === null) {
            throw new AuthorizationException('No employee record linked to your account.');
        }

        DB::transaction(function () use ($employee, $attributes): void {
            foreach ($attributes as $dimension => $value) {
                DeiAttribute::query()->updateOrCreate(
                    ['employee_id' => $employee->id, 'dimension' => $dimension],
                    ['value' => $value, 'consented_at' => now()],
                );
            }

            ConsentLog::create([
                'user_id' => Auth::guard('web')->id(),
                'data_category' => 'dei-attributes',
                'consented_at' => now(),
            ]);
        });
    }
}
