<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalPeriod extends Model
{
    use BelongsToCompany, HasFactory, HasUlids;

    protected $table = 'fin_fiscal_periods';

    protected $fillable = ['company_id', 'period', 'status', 'closed_by', 'closed_at'];

    protected function casts(): array
    {
        return ['closed_at' => 'datetime'];
    }
}
