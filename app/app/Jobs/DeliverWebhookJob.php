<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Core\WebhookEndpoint;
use App\Services\Core\WebhookDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly WebhookEndpoint $endpoint,
        public readonly string $event,
        public readonly array $payload,
    ) {}

    public function handle(WebhookDeliveryService $service): void
    {
        $service->deliver($this->endpoint, $this->event, $this->payload);
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }
}
