<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\WebhookEndpointFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $company_id
 * @property string $url
 * @property string $secret
 * @property list<string> $events
 * @property bool $is_active
 * @property int $consecutive_failures
 */
class WebhookEndpoint extends Model
{
    /** @use HasFactory<WebhookEndpointFactory> */
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'url',
        'secret',
        'events',
        'is_active',
        'consecutive_failures',
    ];

    /** @var list<string> */
    protected $hidden = ['secret'];

    protected function casts(): array
    {
        return [
            'secret' => 'encrypted',
            'events' => 'array',
            'is_active' => 'boolean',
            'consecutive_failures' => 'integer',
        ];
    }

    /** @return HasMany<WebhookDelivery, $this> */
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class, 'endpoint_id');
    }
}
