<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use App\Enums\Crm\ContactType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CrmContact extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'crm_company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_title',
        'type',
        'tags',
        'notes',
        'linkedin_url',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
            'tags' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'type', 'crm_company_id'])
            ->logOnlyDirty();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function crmCompany(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CrmContactCustomField::class, 'company_id', 'company_id');
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CrmContactCustomFieldValue::class, 'crm_contact_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(CrmActivity::class, 'subject');
    }
}
