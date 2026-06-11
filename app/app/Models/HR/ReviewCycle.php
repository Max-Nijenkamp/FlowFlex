<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\ReviewCycle\ReviewCycleState;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class ReviewCycle extends Model
{
    use BelongsToCompany, HasStates, HasUlids, SoftDeletes;

    protected $table = 'hr_review_cycles';

    protected $fillable = ['company_id', 'name', 'period_start', 'period_end', 'type', 'rating_scale', 'status'];

    protected function casts(): array
    {
        return [
            'status' => ReviewCycleState::class, 'period_start' => 'date', 'period_end' => 'date', 'rating_scale' => 'array'];
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'cycle_id');
    }
}
