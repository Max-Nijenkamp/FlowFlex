<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Platform-level suppression list (foundation.email). Deliverability is a
 * property of the address, not the tenant — an address bounced for one
 * company is undeliverable for all — so like module_catalog this table
 * deliberately carries no company_id and no BelongsToCompany.
 *
 * A row alone is a counter; only `suppressed_at` blocks sending. Hard
 * bounces and complaints suppress immediately, soft bounces after
 * RecordSoftBounceAction::THRESHOLD delivery delays.
 *
 * @property string $id
 * @property string $email
 * @property string $reason
 * @property int $soft_bounce_count
 * @property ?Carbon $suppressed_at
 */
class EmailSuppression extends Model
{
    use HasUlids;
    use SoftDeletes;

    protected $fillable = ['email', 'reason', 'soft_bounce_count', 'suppressed_at'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'soft_bounce_count' => 'integer',
            'suppressed_at' => 'datetime',
        ];
    }
}
