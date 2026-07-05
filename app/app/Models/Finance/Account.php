<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\AccountFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * GL account (finance.ledger chart of accounts). Undeletable once
 * posted-to; inactive blocks new postings but keeps history.
 *
 * @property string $id
 * @property string $company_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property ?string $parent_account_id
 * @property bool $is_active
 */
class Account extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    public const TYPES = ['asset', 'liability', 'equity', 'revenue', 'expense'];

    protected $table = 'fin_accounts';

    protected $fillable = ['company_id', 'code', 'name', 'type', 'parent_account_id', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @return BelongsTo<Account, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    /** @return HasMany<JournalLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }
}
