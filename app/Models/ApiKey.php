<?php

namespace App\Models;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ApiKey extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'created_by_tenant_id',
        'name',
        'key_hash',
        'key_prefix',
        'scopes',
        'last_used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'scopes'       => 'array',
            'last_used_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'scopes', 'expires_at', 'last_used_at'])
            ->logOnlyDirty();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'created_by_tenant_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public static function generateKey(): array
    {
        $plaintext = 'ff_' . Str::random(37);

        return [
            'key'    => $plaintext,
            'hash'   => hash('sha256', $plaintext),
            'prefix' => substr($plaintext, 0, 12),
        ];
    }
}
