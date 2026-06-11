<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DealHealth extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_deal_health';

    protected $fillable = ['company_id', 'deal_id', 'score', 'factors', 'calculated_at'];

    protected function casts(): array
    {
        return ['score' => 'integer', 'factors' => 'array', 'calculated_at' => 'datetime'];
    }
}
