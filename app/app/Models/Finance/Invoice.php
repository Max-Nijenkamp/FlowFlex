<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\States\Finance\Invoice\InvoiceState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\InvoiceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * Customer invoice (finance.invoicing). Money integer cents; state
 * machine draft → sent → partially_paid → paid / overdue / voided; the
 * number is assigned at send time, gap-free per company.
 *
 * @property string $id
 * @property string $company_id
 * @property string $customer_id
 * @property ?string $invoice_number
 * @property InvoiceState $status
 * @property Carbon $issue_date
 * @property Carbon $due_date
 * @property int $subtotal_cents
 * @property int $tax_total_cents
 * @property int $total_cents
 * @property int $paid_amount_cents
 * @property string $currency
 * @property numeric-string $discount_percent
 * @property ?string $notes
 * @property ?string $recurring_schedule
 * @property ?Carbon $next_recurring_at
 * @property ?string $source_deal_id
 */
class Invoice extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    use HasStates;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_invoices';

    protected $fillable = [
        'company_id', 'customer_id', 'invoice_number', 'status', 'issue_date', 'due_date',
        'subtotal_cents', 'tax_total_cents', 'total_cents', 'paid_amount_cents',
        'currency', 'discount_percent', 'notes', 'recurring_schedule',
        'next_recurring_at', 'source_deal_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => InvoiceState::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal_cents' => 'integer',
            'tax_total_cents' => 'integer',
            'total_cents' => 'integer',
            'paid_amount_cents' => 'integer',
            'discount_percent' => 'decimal:2',
            'next_recurring_at' => 'date',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /** @return HasMany<InvoiceLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function openBalanceCents(): int
    {
        return max(0, $this->total_cents - $this->paid_amount_cents);
    }
}
