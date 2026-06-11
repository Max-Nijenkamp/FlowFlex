<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_shifts';

    protected $fillable = ['company_id', 'employee_id', 'date', 'start_time', 'end_time', 'role', 'status'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
