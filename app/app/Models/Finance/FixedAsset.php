<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_fixed_assets';

    protected $fillable = ['company_id', 'name', 'category', 'cost_cents', 'purchase_date', 'useful_life_months', 'method', 'salvage_cents', 'accumulated_depreciation_cents', 'status', 'it_asset_id', 'disposed_at', 'disposal_proceeds_cents'];

    protected function casts(): array
    {
        return [
            'cost_cents' => 'integer',
            'purchase_date' => 'date',
            'useful_life_months' => 'integer',
            'salvage_cents' => 'integer',
            'accumulated_depreciation_cents' => 'integer',
            'disposed_at' => 'datetime',
            'disposal_proceeds_cents' => 'integer',
        ];
    }

    public function netBookValueCents(): int
    {
        return $this->cost_cents - $this->accumulated_depreciation_cents;
    }

    /** @return HasMany<DepreciationEntry, $this> */
    public function depreciationEntries(): HasMany
    {
        return $this->hasMany(DepreciationEntry::class, 'asset_id');
    }
}
