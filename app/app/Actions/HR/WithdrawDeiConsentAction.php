<?php

declare(strict_types=1);

namespace App\Actions\HR;

use App\Models\ConsentLog;
use App\Models\HR\DeiAttribute;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class WithdrawDeiConsentAction
{
    use AsAction;

    /** Deletes own attributes (hard delete) + logs withdrawal. */
    public function handle(): void
    {
        $employee = Employee::query()->where('user_id', Auth::guard('web')->id())->firstOrFail();

        DB::transaction(function () use ($employee): void {
            DeiAttribute::query()->where('employee_id', $employee->id)->delete();

            ConsentLog::query()
                ->where('user_id', Auth::guard('web')->id())
                ->where('data_category', 'dei-attributes')
                ->whereNull('withdrawn_at')
                ->update(['withdrawn_at' => now()]);
        });
    }
}
