<?php

declare(strict_types=1);

namespace App\Models;

use App\States\BillingInvoice\BillingInvoiceState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\BillingInvoiceFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property int $total_cents
 * @property string $currency
 * @property string|null $stripe_invoice_id
 * @property BillingInvoiceState $status
 * @property Carbon|null $paid_at
 * @property int $dunning_attempts
 * @property Carbon|null $next_dunning_at
 * @property-read Collection<int, BillingInvoiceLine> $lines
 */
class BillingInvoice extends Model
{
    /** @use HasFactory<BillingInvoiceFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'period_start',
        'period_end',
        'total_cents',
        'currency',
        'stripe_invoice_id',
        'status',
        'paid_at',
        'dunning_attempts',
        'next_dunning_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_cents' => 'integer',
            'status' => BillingInvoiceState::class,
            'paid_at' => 'datetime',
            'dunning_attempts' => 'integer',
            'next_dunning_at' => 'datetime',
        ];
    }

    /** @return HasMany<BillingInvoiceLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(BillingInvoiceLine::class, 'invoice_id');
    }
}
