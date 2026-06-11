<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashflowProjection extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_cashflow_projections';

    protected $fillable = ['company_id', 'week_start', 'opening_cents', 'inflow_cents', 'outflow_cents', 'closing_cents', 'is_actual'];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'opening_cents' => 'integer',
            'inflow_cents' => 'integer',
            'outflow_cents' => 'integer',
            'closing_cents' => 'integer',
            'is_actual' => 'boolean',
        ];
    }

    /** @return HasMany<CashflowItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(CashflowItem::class, 'projection_id');
    }
}
