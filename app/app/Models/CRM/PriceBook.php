<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceBook extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_price_books';

    protected $fillable = ['company_id', 'name', 'currency', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    /** @return HasMany<PriceBookEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(PriceBookEntry::class, 'price_book_id');
    }
}
