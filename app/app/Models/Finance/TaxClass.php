<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxClass extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_tax_classes';

    protected $fillable = ['company_id', 'name', 'default_rate_id'];

    /** @return BelongsTo<TaxRate, $this> */
    public function defaultRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'default_rate_id');
    }
}
