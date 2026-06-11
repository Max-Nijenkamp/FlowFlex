<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forecast extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_forecasts';

    protected $fillable = ['company_id', 'name', 'scenario', 'fiscal_year', 'assumptions'];

    protected function casts(): array
    {
        return ['fiscal_year' => 'integer', 'assumptions' => 'array'];
    }

    /** @return HasMany<ForecastLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(ForecastLine::class, 'forecast_id');
    }
}
