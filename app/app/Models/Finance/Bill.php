<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\States\Finance\Bill\BillState;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Bill extends Model
{
    use BelongsToCompany, HasStates, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'fin_bills';

    protected $fillable = ['company_id', 'supplier_id', 'bill_number', 'po_id', 'amount_cents', 'currency', 'bill_date', 'due_date', 'status', 'early_discount_percent', 'early_discount_until', 'approved_by', 'paid_at', 'payment_run_id'];

    protected function casts(): array
    {
        return [
            'status' => BillState::class,
            'amount_cents' => 'integer',
            'bill_date' => 'date',
            'due_date' => 'date',
            'early_discount_percent' => 'float',
            'early_discount_until' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /** @return HasMany<BillLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(BillLine::class, 'bill_id');
    }
}
