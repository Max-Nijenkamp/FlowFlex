<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_feedback';

    protected $fillable = ['company_id', 'from_employee_id', 'to_employee_id', 'type', 'message', 'visibility', 'related_goal_id'];
}
