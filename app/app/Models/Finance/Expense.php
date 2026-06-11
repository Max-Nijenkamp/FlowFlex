<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\States\Finance\Expense\ExpenseState;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string|null $employee_id
 * @property string $category_id
 * @property int $amount_cents
 * @property string $currency
 * @property Carbon $expense_date
 * @property string $merchant
 * @property ExpenseState $status
 * @property bool $is_over_limit
 * @property string|null $approved_by
 * @property-read ExpenseCategory $category
 */
class Expense extends Model
{
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $table = 'fin_expenses';

    protected $fillable = [
        'company_id', 'user_id', 'employee_id', 'category_id', 'amount_cents', 'currency',
        'expense_date', 'merchant', 'description', 'status', 'is_over_limit',
        'approved_by', 'reimbursed_via',
    ];

    protected function casts(): array
    {
        return [
            'status' => ExpenseState::class,
            'amount_cents' => 'integer',
            'expense_date' => 'date',
            'is_over_limit' => 'boolean',
        ];
    }

    /** @return BelongsTo<ExpenseCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
