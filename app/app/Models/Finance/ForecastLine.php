<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ForecastLine extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_forecast_lines';

    protected $fillable = ['company_id', 'forecast_id', 'account_id', 'period', 'projected_cents'];

    protected function casts(): array
    {
        return ['projected_cents' => 'integer'];
    }
}
