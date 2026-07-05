<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice line (finance.invoicing). Tax rounded per line, lines summed
 * (spec rounding rule); 21% NL default until finance.tax ships.
 *
 * @property string $id
 * @property string $company_id
 * @property string $invoice_id
 * @property string $description
 * @property numeric-string $quantity
 * @property int $unit_price_cents
 * @property numeric-string $tax_rate_percent
 * @property int $tax_cents
 * @property int $line_total_cents
 */
class InvoiceLine extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_invoice_lines';

    protected $fillable = [
        'company_id', 'invoice_id', 'description', 'quantity',
        'unit_price_cents', 'tax_rate_percent', 'tax_cents', 'line_total_cents',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price_cents' => 'integer',
            'tax_rate_percent' => 'decimal:2',
            'tax_cents' => 'integer',
            'line_total_cents' => 'integer',
        ];
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
