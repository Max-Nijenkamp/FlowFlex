<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_tax_rates';

    protected $fillable = ['company_id', 'name', 'rate_basis_points', 'type', 'jurisdiction', 'is_reverse_charge', 'is_active'];

    protected function casts(): array
    {
        return ['rate_basis_points' => 'integer', 'is_reverse_charge' => 'boolean', 'is_active' => 'boolean'];
    }
}
