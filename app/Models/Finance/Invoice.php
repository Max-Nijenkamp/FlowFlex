<?php

namespace App\Models\Finance;

use App\Concerns\BelongsToCompany;
use App\Enums\Finance\InvoiceStatus;
use App\Models\Crm\CrmContact;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'contact_id',
        'number',
        'currency',
        'issue_date',
        'due_date',
        'status',
        'notes',
        'discount_type',
        'discount_value',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total',
        'paid_amount',
        'is_recurring',
        'recurring_invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'     => 'date',
            'due_date'       => 'date',
            'status'         => InvoiceStatus::class,
            'discount_value' => 'decimal:2',
            'tax_rate'       => 'decimal:2',
            'subtotal'       => 'decimal:2',
            'tax_amount'     => 'decimal:2',
            'total'          => 'decimal:2',
            'paid_amount'    => 'decimal:2',
            'is_recurring'   => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total', 'paid_amount', 'due_date'])
            ->logOnlyDirty();
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function creditNote(): HasOne
    {
        return $this->hasOne(CreditNote::class);
    }

    public function emailEvents(): HasMany
    {
        return $this->hasMany(InvoiceEmailEvent::class);
    }

    public function recurringInvoice(): BelongsTo
    {
        return $this->belongsTo(RecurringInvoice::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }
}
