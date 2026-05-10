<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'notification_log';

    protected $fillable = [
        'company_id',
        'user_id',
        'event_type',
        'channel',
        'status',
        'payload',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
