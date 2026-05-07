<?php

namespace App\Models;

use App\Concerns\InteractsWithAddresses;
use App\Contracts\HasAddresses;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
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
class Tenant extends Authenticatable implements FilamentUser, HasAddresses, HasName
{
    use HasFactory, HasRoles, HasUlids, InteractsWithAddresses, LogsActivity, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_enabled'        => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return false;
        }

        $company = $this->company;

        if (! $this->is_enabled || ! $company->is_enabled) {
            return false;
        }

        if ($panel->getId() === 'workspace') {
            return true;
        }

        return Cache::remember(
            "company:{$this->company_id}:panel:{$panel->getId()}:access",
            now()->addMinutes(5),
            fn () => $company->hasModuleForPanel($panel->getId())
        );
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function fullName(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    /**
     * Eloquent attribute accessor so `$tenant->full_name` works in Filament table columns.
     */
    public function getFullNameAttribute(): string
    {
        return $this->fullName();
    }

    public function getFilamentName(): string
    {
        return $this->fullName();
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->company->setting($key, $default);
    }
}
