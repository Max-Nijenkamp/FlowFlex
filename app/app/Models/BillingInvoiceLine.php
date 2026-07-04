<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Snapshot line at billing time (core.billing-engine/monthly-invoicing).
 *
 * @property string $id
 * @property string $invoice_id
 * @property string $company_id
 * @property string $module_key
 * @property string $module_name
 * @property int $user_count
 * @property int $unit_price_cents
 * @property int $line_total_cents
 */
class BillingInvoiceLine extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id', 'company_id', 'module_key', 'module_name',
        'user_count', 'unit_price_cents', 'line_total_cents',
    ];

    /** @return BelongsTo<BillingInvoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }
}
