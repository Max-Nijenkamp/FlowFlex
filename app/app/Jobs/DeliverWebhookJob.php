<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;

    /** @var list<int> */
    public array $backoff = [30, 120, 600];

    private const int AUTO_DISABLE_AFTER = 20;

    /** @param array<string, mixed> $payload */
    public function __construct(
        public readonly string $endpointId,
        public readonly string $eventType,
        public readonly array $payload,
        public readonly string $deliveryId,
    ) {
        $this->onQueue('webhooks');
    }

    public function handle(): void
    {
        $endpoint = WebhookEndpoint::query()->withoutGlobalScopes()->find($this->endpointId);
        $delivery = WebhookDelivery::query()->withoutGlobalScopes()->find($this->deliveryId);

        if ($endpoint === null || $delivery === null || ! $endpoint->is_active) {
            return;
        }

        $body = json_encode($this->payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $body, $endpoint->secret);

        $response = Http::timeout(10)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-FlowFlex-Signature' => "sha256={$signature}",
                'X-FlowFlex-Event' => $this->eventType,
            ])
            ->withBody($body, 'application/json')
            ->post($endpoint->url);

        $delivery->forceFill([
            'response_status' => $response->status(),
            'attempts' => $delivery->attempts + 1,
        ]);

        if ($response->successful()) {
            $delivery->forceFill(['delivered_at' => now()])->save();
            $endpoint->forceFill(['consecutive_failures' => 0])->save();

            return;
        }

        $delivery->save();
        $failures = $endpoint->consecutive_failures + 1;
        $endpoint->forceFill([
            'consecutive_failures' => $failures,
            'is_active' => $failures < self::AUTO_DISABLE_AFTER,
        ])->save();

        // Trigger queue retry with backoff.
        $this->release($this->backoff[min($this->attempts() - 1, count($this->backoff) - 1)]);
    }
}
