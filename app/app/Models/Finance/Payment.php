<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\PaymentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Recorded payment against an invoice (finance.invoicing). Amount never
 * exceeds the open balance; each payment posts a balanced journal entry.
 *
 * @property string $id
 * @property string $company_id
 * @property string $invoice_id
 * @property int $amount_cents
 * @property Carbon $payment_date
 * @property ?string $method
 * @property ?string $reference
 * @property ?string $recorded_by
 */
class Payment extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_payments';

    protected $fillable = [
        'company_id', 'invoice_id', 'amount_cents', 'payment_date',
        'method', 'reference', 'recorded_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'payment_date' => 'date'];
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
