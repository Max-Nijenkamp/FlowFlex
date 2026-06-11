<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ReviewGoal extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_review_goals';

    protected $fillable = ['company_id', 'review_id', 'employee_id', 'title', 'description', 'progress_percent', 'rating'];

    protected function casts(): array
    {
        return ['progress_percent' => 'integer', 'rating' => 'float'];
    }
}
