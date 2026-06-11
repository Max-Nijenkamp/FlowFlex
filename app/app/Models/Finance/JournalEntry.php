<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'fin_journal_entries';

    protected $fillable = ['company_id', 'reference', 'description', 'entry_date', 'status', 'source_type', 'source_id', 'created_by'];

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
