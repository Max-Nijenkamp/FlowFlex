<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\States\Finance\Expense\ExpenseState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\ExpenseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * Expense claim (finance.expenses). State machine draft → submitted →
 * approved|rejected → reimbursed; is_over_limit flags policy breaches
 * against the category limit.
 *
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property ?string $employee_id
 * @property string $category_id
 * @property int $amount_cents
 * @property string $currency
 * @property Carbon $expense_date
 * @property string $merchant
 * @property ?string $description
 * @property ExpenseState $status
 * @property bool $is_over_limit
 * @property ?string $approved_by
 * @property ?string $rejection_reason
 * @property ?string $report_id
 * @property ?string $reimbursed_via
 */
class Expense extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    use HasStates;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_expenses';

    protected $fillable = [
        'company_id', 'user_id', 'employee_id', 'category_id', 'amount_cents',
        'currency', 'expense_date', 'merchant', 'description', 'status',
        'is_over_limit', 'approved_by', 'rejection_reason', 'report_id', 'reimbursed_via',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'expense_date' => 'date',
            'status' => ExpenseState::class,
            'is_over_limit' => 'boolean',
        ];
    }

    /** @return BelongsTo<ExpenseCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<ExpenseReport, $this> */
    public function report(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class, 'report_id');
    }
}
