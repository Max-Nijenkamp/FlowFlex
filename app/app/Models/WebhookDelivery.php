<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Append-only delivery log (pruned after 30 days).
 *
 * @property string $id
 * @property string $endpoint_id
 * @property string $company_id
 * @property string $event_type
 * @property array<string, mixed> $payload
 * @property int|null $response_status
 * @property int $attempts
 * @property Carbon|null $delivered_at
 */
class WebhookDelivery extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'endpoint_id',
        'company_id',
        'event_type',
        'payload',
        'response_status',
        'attempts',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response_status' => 'integer',
            'attempts' => 'integer',
            'delivered_at' => 'datetime',
        ];
    }
}
