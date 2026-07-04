<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One row per user × notification type (core.notifications/preferences).
 * Missing row = platform defaults (both channels on).
 *
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string $notification_type
 * @property bool $in_app_enabled
 * @property bool $email_enabled
 */
class NotificationPreference extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'user_id', 'notification_type', 'in_app_enabled', 'email_enabled',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
