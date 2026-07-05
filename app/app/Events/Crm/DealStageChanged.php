<?php

declare(strict_types=1);

namespace App\Events\Crm;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Board move — broadcast on the per-company channel so other open boards
 * patch the card live (crm.pipeline/realtime-sync). Degrades silently
 * without Reverb creds.
 */
class DealStageChanged implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(
        public readonly string $company_id,
        public readonly string $deal_id,
        public readonly string $from_stage_id,
        public readonly string $to_stage_id,
        public readonly string $moved_by,
    ) {}

    /** @return array<int, PrivateChannel> */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("company.{$this->company_id}")];
    }

    public function broadcastAs(): string
    {
        return 'crm.deal-stage-changed';
    }
}
