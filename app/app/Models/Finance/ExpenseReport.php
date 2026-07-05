<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Groups expenses for bulk submission (finance.expenses). Submitting a
 * report cascades submit to its member drafts.
 *
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string $title
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property string $status
 * @property ?Carbon $submitted_at
 * @property ?Carbon $approved_at
 */
class ExpenseReport extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_expense_reports';

    protected $fillable = [
        'company_id', 'user_id', 'title', 'period_start', 'period_end',
        'status', 'submitted_at', 'approved_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /** @return HasMany<Expense, $this> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'report_id');
    }

    /** @return BelongsTo<User, $this> */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
