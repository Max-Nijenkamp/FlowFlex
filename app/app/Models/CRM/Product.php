<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToCompany, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'crm_products';

    protected $fillable = ['company_id', 'name', 'sku', 'description', 'unit', 'standard_price_cents', 'cost_cents', 'is_active'];

    protected function casts(): array
    {
        return ['standard_price_cents' => 'integer', 'cost_cents' => 'integer', 'is_active' => 'boolean'];
    }
}
