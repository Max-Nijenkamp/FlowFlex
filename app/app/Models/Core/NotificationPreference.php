<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Core\NotificationPreferenceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string $notification_type
 * @property bool $in_app_enabled
 * @property bool $email_enabled
 */
class NotificationPreference extends Model
{
    /** @use HasFactory<NotificationPreferenceFactory> */
    use BelongsToCompany, HasFactory, HasUlids;

    protected $fillable = [
        'company_id',
        'user_id',
        'notification_type',
        'in_app_enabled',
        'email_enabled',
    ];

    protected function casts(): array
    {
        return [
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
        ];
    }
}
