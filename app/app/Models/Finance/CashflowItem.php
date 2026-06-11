<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class CashflowItem extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_cashflow_items';

    protected $fillable = ['company_id', 'projection_id', 'type', 'source', 'source_id', 'description', 'amount_cents', 'expected_date'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'expected_date' => 'date'];
    }
}
