<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingInvoice extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'status',
        'invoice_pdf_url',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at'  => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
