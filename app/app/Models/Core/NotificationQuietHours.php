<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationQuietHours extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'timezone',
        'days_of_week',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        $now = now()->setTimezone($this->timezone ?? 'UTC');
        $start = \Carbon\Carbon::parse($this->start_time)->setDateFrom($now)->setTimezone($this->timezone ?? 'UTC');
        $end = \Carbon\Carbon::parse($this->end_time)->setDateFrom($now)->setTimezone($this->timezone ?? 'UTC');

        if ($end->lt($start)) {
            $end->addDay();
        }

        return $now->between($start, $end);
    }
}
