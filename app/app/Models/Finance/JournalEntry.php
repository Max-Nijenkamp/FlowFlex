<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\JournalEntryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Double-entry journal entry (finance.ledger). Posted entries are
 * immutable — corrections happen via LedgerService::reverse only.
 *
 * @property string $id
 * @property string $company_id
 * @property string $reference
 * @property string $description
 * @property Carbon $entry_date
 * @property string $status
 * @property ?string $source_type
 * @property ?string $source_id
 * @property ?string $created_by
 */
class JournalEntry extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<JournalEntryFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_journal_entries';

    protected $fillable = [
        'company_id', 'reference', 'description', 'entry_date',
        'status', 'source_type', 'source_id', 'created_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['entry_date' => 'date'];
    }

    /** @return HasMany<JournalLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }
}
