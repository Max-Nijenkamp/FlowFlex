<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasUlids;

    protected $fillable = [
        'key',
        'name',
        'description',
        'domain',
        'panel_id',
        'icon',
        'color',
        'sort_order',
        'is_core',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_core'      => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function subModules(): HasMany
    {
        return $this->hasMany(SubModule::class)->orderBy('sort_order');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_module')
            ->withPivot(['is_enabled', 'enabled_at', 'disabled_at'])
            ->withTimestamps();
    }
}
