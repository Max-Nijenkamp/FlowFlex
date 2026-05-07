<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DocumentShare extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'document_id',
        'share_token',
        'expires_at',
        'password_protected',
        'password_hash',
        'download_only',
        'view_count',
        'created_by_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'         => 'datetime',
            'password_protected' => 'boolean',
            'download_only'      => 'boolean',
            'password_hash'      => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['document_id', 'share_token', 'expires_at', 'password_protected', 'download_only'])
            ->dontLogIfAttributeIsEmpty('password_hash')
            ->logOnlyDirty();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'created_by_tenant_id');
    }
}
