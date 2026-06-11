<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_budget_lines';

    protected $fillable = ['company_id', 'budget_id', 'account_id', 'period', 'budgeted_cents'];

    protected function casts(): array
    {
        return ['budgeted_cents' => 'integer'];
    }
}
