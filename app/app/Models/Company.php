<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'status',
        'timezone',
        'locale',
        'currency',
        'branding',
        'ai_config',
        'trial_ends_at',
        'subscribed_at',
    ];

    protected $casts = [
        'branding'      => 'array',
        'ai_config'     => 'array',
        'trial_ends_at' => 'datetime',
        'subscribed_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function moduleSubscriptions(): HasMany
    {
        return $this->hasMany(CompanyModuleSubscription::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(CompanyFeatureFlag::class);
    }

    public function activeModuleKeys(): array
    {
        return $this->moduleSubscriptions()
            ->where('status', 'active')
            ->pluck('module_key')
            ->toArray();
    }

    public function hasModule(string $key): bool
    {
        return $this->moduleSubscriptions()
            ->where('module_key', $key)
            ->where('status', 'active')
            ->exists();
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trial', 'active'], true);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
