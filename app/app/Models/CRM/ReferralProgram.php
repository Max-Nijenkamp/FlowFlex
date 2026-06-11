<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralProgram extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_referral_programs';

    protected $fillable = ['company_id', 'name', 'referrer_reward', 'referee_reward', 'terms', 'is_active', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return ['referrer_reward' => 'array', 'referee_reward' => 'array', 'is_active' => 'boolean', 'starts_at' => 'date', 'ends_at' => 'date'];
    }
}
