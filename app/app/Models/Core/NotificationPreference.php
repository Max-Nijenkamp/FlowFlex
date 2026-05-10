<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'company_id',
        'event_type',
        'channel',
        'enabled',
        'delivery_mode',
        'digest_time',
        'timezone',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
