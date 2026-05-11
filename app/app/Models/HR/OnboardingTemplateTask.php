<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTemplateTask extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'onboarding_template_tasks';

    protected $fillable = [
        'company_id',
        'template_id',
        'title',
        'description',
        'assignee_role',
        'due_days_after_hire',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'due_days_after_hire' => 'integer',
        'is_required'         => 'boolean',
        'sort_order'          => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }
}
