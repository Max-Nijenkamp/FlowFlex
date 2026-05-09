<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleCatalog extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'module_catalog';

    protected $fillable = [
        'module_key',
        'domain',
        'name',
        'per_user_monthly_price',
        'is_active',
    ];

    protected $casts = [
        'per_user_monthly_price' => 'decimal:2',
        'is_active'              => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanyModuleSubscription::class, 'module_key', 'module_key');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFree(): bool
    {
        return (float) $this->per_user_monthly_price === 0.00;
    }
}
