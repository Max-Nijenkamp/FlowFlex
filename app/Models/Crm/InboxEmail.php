<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InboxEmail extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'shared_inbox_id',
        'crm_contact_id',
        'assigned_tenant_id',
        'message_id',
        'from_email',
        'from_name',
        'subject',
        'body_html',
        'body_text',
        'status',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['shared_inbox_id', 'subject', 'status', 'assigned_tenant_id'])
            ->logOnlyDirty();
    }

    public function sharedInbox(): BelongsTo
    {
        return $this->belongsTo(SharedInbox::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function assignedTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'assigned_tenant_id');
    }
}
