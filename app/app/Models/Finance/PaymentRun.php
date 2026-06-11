<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentRun extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_payment_runs';

    protected $fillable = ['company_id', 'run_date', 'total_cents', 'status'];

    protected function casts(): array
    {
        return ['run_date' => 'date', 'total_cents' => 'integer'];
    }

    /** @return HasMany<Bill, $this> */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'payment_run_id');
    }
}
