<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Expense category = the policy unit (finance.expenses): per-transaction
 * limit + GL posting target.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property ?int $limit_per_transaction_cents
 * @property string $gl_account_id
 */
class ExpenseCategory extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_expense_categories';

    protected $fillable = ['company_id', 'name', 'limit_per_transaction_cents', 'gl_account_id'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['limit_per_transaction_cents' => 'integer'];
    }

    /** @return BelongsTo<Account, $this> */
    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }

    /** @return HasMany<Expense, $this> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
