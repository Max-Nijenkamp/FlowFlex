<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ForecastSnapshot extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_forecast_snapshots';

    protected $fillable = ['company_id', 'owner_id', 'period', 'category', 'amount_cents', 'captured_at'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'captured_at' => 'datetime'];
    }
}
