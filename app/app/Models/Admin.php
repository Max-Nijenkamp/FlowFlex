<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AdminFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * FlowFlex staff account. NOT company-scoped — separate `admin` guard.
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Admin extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory, HasUlids, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
