<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_time_entries';

    protected $fillable = ['company_id', 'employee_id', 'date', 'clock_in', 'clock_out', 'break_minutes', 'total_minutes', 'is_overtime', 'notes', 'timesheet_id'];

    protected function casts(): array
    {
        return ['date' => 'date', 'break_minutes' => 'integer', 'total_minutes' => 'integer', 'is_overtime' => 'boolean'];
    }
}
