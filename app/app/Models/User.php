<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use BelongsToCompany;
    use HasFactory;
    use HasRoles;
    use HasUlids;
    use Notifiable;
    use SoftDeletes;

    protected string $guard_name = 'web';

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'locale',
        'timezone',
        'status',
        'two_factor_enabled',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'last_login_at'      => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password'           => 'hashed',
    ];

    /**
     * Get user's full name.
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInvited(): bool
    {
        return $this->status === 'invited';
    }

    public function isDeactivated(): bool
    {
        return $this->status === 'deactivated';
    }
}
