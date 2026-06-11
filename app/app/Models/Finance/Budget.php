<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_budgets';

    protected $fillable = ['company_id', 'name', 'fiscal_year', 'scope_type', 'scope_id', 'status', 'version'];

    protected function casts(): array
    {
        return ['fiscal_year' => 'integer', 'version' => 'integer'];
    }

    /** @return HasMany<BudgetLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(BudgetLine::class, 'budget_id');
    }
}
