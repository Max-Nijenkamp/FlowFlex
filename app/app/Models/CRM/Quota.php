<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_quotas';

    protected $fillable = ['company_id', 'owner_id', 'period', 'quota_cents', 'currency'];

    protected function casts(): array
    {
        return ['quota_cents' => 'integer'];
    }
}
