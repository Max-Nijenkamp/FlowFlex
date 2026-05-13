<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\HasUlid;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasUlid;
    use BelongsToCompany;
    use SoftDeletes;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'active' || $this->status === 'invited';
    }
}
