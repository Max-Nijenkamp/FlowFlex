<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadcountPlan extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_headcount_plans';

    protected $fillable = ['company_id', 'department_id', 'period', 'target_headcount', 'expected_attrition', 'budgeted_cost_cents', 'currency'];

    protected function casts(): array
    {
        return ['target_headcount' => 'integer', 'expected_attrition' => 'integer', 'budgeted_cost_cents' => 'integer'];
    }
}
