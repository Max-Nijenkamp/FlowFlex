<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillLine extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_bill_lines';

    protected $fillable = ['company_id', 'bill_id', 'description', 'account_id', 'amount_cents'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer'];
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
