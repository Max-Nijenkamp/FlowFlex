<?php

declare(strict_types=1);

namespace App\Events\CRM;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

/** Collaborative Kanban refresh on company.{id}.pipeline (within-domain UI event). */
class DealStageMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly string $company_id,
        public readonly string $deal_id,
        public readonly string $stage_id,
        public readonly string $moved_by,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->company_id}.pipeline");
    }

    public function broadcastAs(): string
    {
        return 'deal.moved';
    }
}
