<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\UserInvitationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $company_id
 * @property string $email
 * @property string $token
 * @property string $role
 * @property string $invited_by
 * @property Carbon|null $accepted_at
 * @property Carbon|null $revoked_at
 * @property Carbon $expires_at
 */
class UserInvitation extends Model
{
    /** @use HasFactory<UserInvitationFactory> */
    use BelongsToCompany, HasFactory, HasUlids;

    protected $fillable = [
        'company_id',
        'email',
        'token',
        'role',
        'invited_by',
        'accepted_at',
        'revoked_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /** @param Builder<self> $query */
    public function scopePending(Builder $query): void
    {
        $query->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function isUsable(): bool
    {
        return $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isFuture();
    }
}
