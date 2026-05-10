<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEndpoint extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'url',
        'events',
        'secret',
        'is_active',
        'failure_count',
        'last_triggered_at',
    ];

    protected $hidden = ['secret'];

    protected $casts = [
        'events'            => 'array',
        'is_active'         => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

}
