<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_reviews';

    protected $fillable = ['company_id', 'cycle_id', 'employee_id', 'reviewer_id', 'type', 'status', 'rating', 'content', 'submitted_at'];

    /** @return BelongsTo<ReviewCycle, $this> */
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(ReviewCycle::class, 'cycle_id');
    }

    protected function casts(): array
    {
        return ['rating' => 'float', 'content' => 'array', 'submitted_at' => 'datetime'];
    }
}
