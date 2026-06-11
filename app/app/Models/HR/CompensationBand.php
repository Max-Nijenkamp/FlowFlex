<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompensationBand extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_compensation_bands';

    protected $fillable = ['company_id', 'job_grade', 'department_id', 'min_salary_cents', 'mid_salary_cents', 'max_salary_cents', 'currency'];

    protected function casts(): array
    {
        return ['min_salary_cents' => 'integer', 'mid_salary_cents' => 'integer', 'max_salary_cents' => 'integer'];
    }
}
