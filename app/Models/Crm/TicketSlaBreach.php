<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TicketSlaBreach extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity;

    protected $fillable = [
        'company_id',
        'ticket_id',
        'ticket_sla_rule_id',
        'type',
        'breached_at',
    ];

    protected function casts(): array
    {
        return [
            'breached_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['ticket_id', 'type', 'breached_at'])
            ->logOnlyDirty();
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ticketSlaRule(): BelongsTo
    {
        return $this->belongsTo(TicketSlaRule::class);
    }
}
