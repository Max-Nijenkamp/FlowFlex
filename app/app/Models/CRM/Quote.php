<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'crm_quotes';

    protected $fillable = ['company_id', 'deal_id', 'contact_id', 'quote_number', 'status', 'total_cents', 'currency', 'valid_until', 'accept_token', 'accepted_at'];

    protected function casts(): array
    {
        return ['total_cents' => 'integer', 'valid_until' => 'date', 'accepted_at' => 'datetime'];
    }

    /** @return HasMany<QuoteLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(QuoteLine::class, 'quote_id');
    }
}
