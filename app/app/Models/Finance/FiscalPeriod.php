<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Fiscal period lock (finance.ledger). A closed period rejects postings
 * dated inside it; reopening is owner-level and audited.
 *
 * @property string $id
 * @property string $company_id
 * @property string $period YYYY-MM
 * @property string $status open | closed
 * @property ?string $closed_by
 * @property ?Carbon $closed_at
 */
class FiscalPeriod extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_fiscal_periods';

    protected $fillable = ['company_id', 'period', 'status', 'closed_by', 'closed_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['closed_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public static function isClosed(string $companyId, Carbon $date): bool
    {
        return self::query()
            ->where('company_id', $companyId)
            ->where('period', $date->format('Y-m'))
            ->where('status', 'closed')
            ->exists();
    }
}
