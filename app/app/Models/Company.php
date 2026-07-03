<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $subscription_status
 * @property string $timezone
 * @property string $locale
 * @property string $currency
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $setup_completed_at
 */
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subscription_status',
        'timezone',
        'locale',
        'currency',
        'trial_ends_at',
        'setup_completed_at',
    ];

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'setup_completed_at' => 'datetime',
        ];
    }
}
