<?php

namespace App\Models;

use App\Concerns\InteractsWithAddresses;
use App\Contracts\HasAddresses;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'company_id',
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'phone',
    'password',
    'is_enabled',
])]
#[Hidden(['password', 'remember_token'])]
class Tenant extends Authenticatable implements FilamentUser, HasAddresses
{
    use HasRoles, HasUlids, InteractsWithAddresses, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_enabled'        => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return false;
        }

        return $this->is_enabled && $this->company->is_enabled;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fullName(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->company->setting($key, $default);
    }
}
