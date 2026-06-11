<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TaxPeriod extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_tax_periods';

    protected $fillable = ['company_id', 'period', 'output_tax_cents', 'input_tax_cents', 'net_payable_cents', 'status'];

    protected function casts(): array
    {
        return ['output_tax_cents' => 'integer', 'input_tax_cents' => 'integer', 'net_payable_cents' => 'integer'];
    }
}
