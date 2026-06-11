<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class VolumeDiscount extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_volume_discounts';

    protected $fillable = ['company_id', 'product_id', 'min_quantity', 'discount_percent'];

    protected function casts(): array
    {
        return ['min_quantity' => 'float', 'discount_percent' => 'float'];
    }
}
