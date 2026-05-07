<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Ticket extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'crm_contact_id',
        'crm_company_id',
        'subject',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'sla_breach_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => TicketStatus::class,
            'priority'     => TicketPriority::class,
            'resolved_at'  => 'datetime',
            'sla_breach_at'=> 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['subject', 'status', 'priority', 'assigned_to', 'resolved_at'])
            ->logOnlyDirty();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function crmCompany(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function slaBreaches(): HasMany
    {
        return $this->hasMany(TicketSlaBreach::class);
    }

    public function csatSurveys(): HasMany
    {
        return $this->hasMany(CsatSurvey::class);
    }
}
