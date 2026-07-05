<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Imported bank statement row (finance.bank). import_hash dedupes
 * re-imports; reconciliation links to a journal line.
 *
 * @property string $id
 * @property string $company_id
 * @property string $bank_account_id
 * @property Carbon $transaction_date
 * @property string $description
 * @property int $amount_cents signed
 * @property string $import_hash
 * @property ?Carbon $reconciled_at
 * @property ?string $journal_line_id
 */
class BankTransaction extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_bank_transactions';

    protected $fillable = [
        'company_id', 'bank_account_id', 'transaction_date', 'description',
        'amount_cents', 'import_hash', 'reconciled_at', 'journal_line_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount_cents' => 'integer',
            'reconciled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<BankAccount, $this> */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /** @return BelongsTo<JournalLine, $this> */
    public function journalLine(): BelongsTo
    {
        return $this->belongsTo(JournalLine::class, 'journal_line_id');
    }
}
