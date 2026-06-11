<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PlannedRole extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_planned_roles';

    protected $fillable = ['plan_id', 'company_id', 'title', 'target_start_date', 'budgeted_salary_cents', 'status', 'requisition_id'];

    protected function casts(): array
    {
        return ['target_start_date' => 'date', 'budgeted_salary_cents' => 'integer'];
    }
}
