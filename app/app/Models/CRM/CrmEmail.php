<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmEmail extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_emails';

    protected $fillable = ['company_id', 'connection_id', 'contact_id', 'deal_id', 'direction', 'subject', 'body', 'visibility', 'message_id', 'thread_id', 'tracking_token', 'sent_at', 'opened_at', 'clicked_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'opened_at' => 'datetime', 'clicked_at' => 'datetime'];
    }

    /** @return BelongsTo<EmailConnection, $this> */
    public function connection(): BelongsTo
    {
        return $this->belongsTo(EmailConnection::class, 'connection_id');
    }

    /** Private emails readable by the connection owner only — not even view-any. */
    public function scopeVisibleTo(Builder $query, string $userId): Builder
    {
        return $query->where(fn ($q) => $q
            ->where('visibility', 'shared')
            ->orWhereHas('connection', fn ($c) => $c->where('user_id', $userId)));
    }
}
