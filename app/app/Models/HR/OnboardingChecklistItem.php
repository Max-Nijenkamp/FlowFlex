<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingChecklistItem extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'onboarding_checklist_items';

    protected $fillable = [
        'company_id',
        'checklist_id',
        'title',
        'description',
        'assignee_id',
        'due_date',
        'completed_at',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'is_required'  => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(OnboardingChecklist::class, 'checklist_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
