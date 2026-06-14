<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\Timesheet\TimesheetState;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Timesheet extends Model
{
    use BelongsToCompany, HasStates, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'hr_timesheets';

    protected $fillable = ['company_id', 'employee_id', 'week_start', 'total_minutes', 'status', 'approved_by', 'approved_at'];

    protected function casts(): array
    {
        return [
            'status' => TimesheetState::class, 'week_start' => 'date', 'total_minutes' => 'integer', 'approved_at' => 'datetime'];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** @return HasMany<TimeEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'timesheet_id');
    }
}
