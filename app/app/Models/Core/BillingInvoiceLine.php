<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
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
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'invoice_id',
        'company_id',
        'module_key',
        'module_name',
        'user_count',
        'unit_price_cents',
        'line_total_cents',
    ];

    protected function casts(): array
    {
        return [
            'user_count' => 'integer',
            'unit_price_cents' => 'integer',
            'line_total_cents' => 'integer',
        ];
    }

    /** @return BelongsTo<BillingInvoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }
}
