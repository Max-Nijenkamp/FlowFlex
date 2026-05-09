<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAnnouncement extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'title',
        'body',
        'target',
        'target_value',
        'created_by',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->sent_at === null;
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function isTargetedToCompany(): bool
    {
        return $this->target === 'company';
    }

    public function scopeDraft($query)
    {
        return $query->whereNull('sent_at');
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }
}
