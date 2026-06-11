<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobRequisition extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_job_requisitions';

    protected $fillable = ['company_id', 'title', 'description', 'employment_type', 'status', 'slug', 'open_date', 'headcount', 'department_id'];

    protected function casts(): array
    {
        return ['open_date' => 'date', 'headcount' => 'integer'];
    }
}
