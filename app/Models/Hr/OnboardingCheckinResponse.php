<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingCheckinResponse extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'checkin_id',
        'respondent_tenant_id',
        'responses',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'responses' => 'array',
        ];
    }

    public function checkin(): BelongsTo
    {
        return $this->belongsTo(OnboardingCheckin::class, 'checkin_id');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'respondent_tenant_id');
    }
}
