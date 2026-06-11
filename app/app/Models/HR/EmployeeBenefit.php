<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class EmployeeBenefit extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_employee_benefits';

    protected $fillable = ['company_id', 'employee_id', 'benefit_id', 'enrolled_at', 'unenrolled_at'];

    protected function casts(): array
    {
        return ['enrolled_at' => 'datetime', 'unenrolled_at' => 'datetime'];
    }
}
