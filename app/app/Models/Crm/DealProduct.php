<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\DealProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Deal line item (crm.deals). product_id stays null until crm.pricing
 * ships — free-text description lines are the v1 path.
 *
 * @property string $id
 * @property string $company_id
 * @property string $deal_id
 * @property ?string $product_id
 * @property string $description
 * @property numeric-string $quantity
 * @property int $unit_price_cents
 * @property numeric-string $discount_percent
 */
class DealProduct extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<DealProductFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_deal_products';

    protected $fillable = [
        'company_id', 'deal_id', 'product_id', 'description',
        'quantity', 'unit_price_cents', 'discount_percent',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price_cents' => 'integer',
            'discount_percent' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Deal, $this> */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
}
