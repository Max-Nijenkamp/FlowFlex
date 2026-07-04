<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EmailSuppression;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Delivery delay (greylisting, full mailbox) is only a signal, not proof —
 * count them per address and suppress once the pattern repeats
 * (foundation.email suppression list, Gmail/Microsoft sender requirements).
 */
class RecordSoftBounceAction
{
    use AsAction;

    public const int THRESHOLD = 3;

    public function handle(string $email): void
    {
        $row = EmailSuppression::query()->firstOrCreate(
            ['email' => $email],
            ['reason' => 'soft-bounce'],
        );

        $row->soft_bounce_count++;
        $row->save();

        if ($row->soft_bounce_count >= self::THRESHOLD && $row->suppressed_at === null) {
            $row->update(['reason' => 'soft-bounce', 'suppressed_at' => now()]);
        }
    }
}
