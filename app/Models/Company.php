<?php

namespace App\Models;

use App\Concerns\InteractsWithAddresses;
use App\Contracts\HasAddresses;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'slug',
    'email',
    'phone',
    'website',
    'timezone',
    'locale',
    'settings',
    'is_enabled',
])]
class Company extends Model implements HasAddresses
{
    use HasUlids, InteractsWithAddresses, SoftDeletes;

    protected function casts(): array
    {
        return [
            'settings'   => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
