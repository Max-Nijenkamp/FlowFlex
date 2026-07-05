<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\BankAccountFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Bank account (finance.bank). account_number + iban are encrypted at
 * rest (patterns/encryption); iban_last4 exists for display. Maps to a
 * GL asset account for reconciliation.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $bank_name
 * @property ?string $account_number
 * @property ?string $iban
 * @property ?string $iban_last4
 * @property string $currency
 * @property string $gl_account_id
 * @property int $current_balance_cents
 */
class BankAccount extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<BankAccountFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_bank_accounts';

    protected $fillable = [
        'company_id', 'name', 'bank_name', 'account_number', 'iban',
        'iban_last4', 'currency', 'gl_account_id', 'current_balance_cents',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'iban' => 'encrypted',
            'current_balance_cents' => 'integer',
        ];
    }

    /** @return BelongsTo<Account, $this> */
    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }

    /** @return HasMany<BankTransaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }
}
