<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One debit or credit line (finance.ledger). Exactly one of
 * debit_cents/credit_cents is non-zero per line.
 *
 * @property string $id
 * @property string $company_id
 * @property string $journal_entry_id
 * @property string $account_id
 * @property int $debit_cents
 * @property int $credit_cents
 * @property ?string $description
 */
class JournalLine extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_journal_lines';

    protected $fillable = [
        'company_id', 'journal_entry_id', 'account_id',
        'debit_cents', 'credit_cents', 'description',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['debit_cents' => 'integer', 'credit_cents' => 'integer'];
    }

    /** @return BelongsTo<JournalEntry, $this> */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
