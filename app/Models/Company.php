<?php

namespace App\Models;

use App\Concerns\InteractsWithAddresses;
use App\Contracts\HasAddresses;
use App\Enums\Language;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
    use HasUlids, InteractsWithAddresses, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'locale'     => Language::class,
            'settings'   => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'company_module')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at'])
            ->withTimestamps();
    }

    public function hasModuleForPanel(string $panelId): bool
    {
        return $this->modules()
            ->wherePivot('is_enabled', true)
            ->where('panel_id', $panelId)
            ->exists();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
