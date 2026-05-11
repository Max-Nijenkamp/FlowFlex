<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingChecklist extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'onboarding_checklists';

    protected $fillable = [
        'company_id',
        'employee_id',
        'template_id',
        'start_date',
        'target_completion_date',
        'completed_at',
    ];

    protected $casts = [
        'start_date'             => 'date',
        'target_completion_date' => 'date',
        'completed_at'           => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OnboardingChecklistItem::class, 'checklist_id')
            ->orderBy('sort_order');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->items()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->items()->whereNotNull('completed_at')->count();

        return (int) round(($completed / $total) * 100);
    }
}
