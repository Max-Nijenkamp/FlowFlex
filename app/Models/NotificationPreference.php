<?php

namespace App\Models;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'tenant_id',
        'notification_type',
        'channels',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'channels'   => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
