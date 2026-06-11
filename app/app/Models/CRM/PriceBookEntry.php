<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PriceBookEntry extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_price_book_entries';

    protected $fillable = ['company_id', 'price_book_id', 'product_id', 'price_cents', 'valid_from', 'valid_until'];

    protected function casts(): array
    {
        return ['price_cents' => 'integer', 'valid_from' => 'date', 'valid_until' => 'date'];
    }
}
