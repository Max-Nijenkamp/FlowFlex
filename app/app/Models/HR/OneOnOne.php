<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OneOnOne extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_one_on_ones';

    protected $fillable = ['company_id', 'manager_id', 'employee_id', 'meeting_date', 'agenda', 'notes', 'action_items'];

    protected function casts(): array
    {
        return ['meeting_date' => 'date', 'action_items' => 'array'];
    }
}
