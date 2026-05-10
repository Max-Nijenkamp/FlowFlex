<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupWizardProgress extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'company_id',
        'completed_steps',
        'current_step',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'completed_steps' => 'array',
        'completed'       => 'boolean',
        'completed_at'    => 'datetime',
    ];

    public function hasStep(string $step): bool
    {
        return in_array($step, $this->completed_steps ?? [], true);
    }

    public function completeStep(string $step): void
    {
        $steps = $this->completed_steps ?? [];
        if (! in_array($step, $steps, true)) {
            $steps[] = $step;
        }
        $this->update(['completed_steps' => $steps]);
    }

    public static function steps(): array
    {
        return ['welcome', 'company', 'team', 'modules', 'branding'];
    }
}
