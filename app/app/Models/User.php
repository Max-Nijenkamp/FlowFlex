<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string $company_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property bool $two_factor_enabled
 * @property bool $email_deliverable
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $last_login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $full_name
 * @property-read Company $company
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use BelongsToCompany, HasApiTokens, HasFactory, HasRoles, HasUlids, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'two_factor_enabled',
        'email_deliverable',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'email_deliverable' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Tenant users may reach the /app workspace + domain panels (not /admin).
        // Per-resource gating is handled by canAccess() (filament-patterns #1).
        return $panel->getId() !== 'admin';
    }
}
