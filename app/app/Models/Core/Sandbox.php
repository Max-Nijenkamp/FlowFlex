<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sandbox extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'status',
        'database_name',
        'redis_prefix',
        's3_prefix',
        'subdomain',
        'seed_type',
        'provisioned_at',
        'last_synced_at',
        'reset_scheduled_at',
    ];

    protected $casts = [
        'provisioned_at'      => 'datetime',
        'last_synced_at'      => 'datetime',
        'reset_scheduled_at'  => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
