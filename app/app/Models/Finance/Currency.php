<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_currencies';

    protected $fillable = ['company_id', 'code', 'symbol', 'minor_unit_digits', 'is_active'];

    protected function casts(): array
    {
        return ['minor_unit_digits' => 'integer', 'is_active' => 'boolean'];
    }
}
