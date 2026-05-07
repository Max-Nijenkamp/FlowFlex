<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use App\Enums\Crm\DealStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Deal extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'crm_contact_id',
        'crm_company_id',
        'pipeline_id',
        'deal_stage_id',
        'title',
        'value',
        'currency',
        'status',
        'close_probability',
        'expected_close_date',
        'closed_at',
        'lost_reason',
        'owner_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'value'               => 'decimal:2',
            'status'              => DealStatus::class,
            'close_probability'   => 'integer',
            'expected_close_date' => 'date',
            'closed_at'           => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'value', 'status', 'close_probability', 'expected_close_date', 'closed_at', 'lost_reason'])
            ->logOnlyDirty();
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DealStage::class, 'deal_stage_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function crmCompany(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(DealNote::class);
    }
}
