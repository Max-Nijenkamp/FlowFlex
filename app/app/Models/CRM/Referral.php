<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_referrals';

    protected $fillable = ['company_id', 'program_id', 'referrer_contact_id', 'referral_code', 'referee_email', 'referee_contact_id', 'status', 'converted_at', 'rewarded_at'];

    protected function casts(): array
    {
        return ['converted_at' => 'datetime', 'rewarded_at' => 'datetime'];
    }
}
