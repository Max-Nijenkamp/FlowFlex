<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_salary_history';

    protected $fillable = ['company_id', 'employee_id', 'amount_raw', 'salary_band', 'effective_date', 'reason', 'changed_by'];

    /** @var list<string> */
    protected $hidden = ['amount_raw'];

    protected function casts(): array
    {
        return ['amount_raw' => 'encrypted', 'effective_date' => 'date'];
    }
}
