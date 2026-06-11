<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Jobs\DeliverWebhookJob;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Universal dispatcher: any cross-domain event carrying a scalar company_id
 * fans out to that company's active, subscribed webhook endpoints.
 * Registered per-event in CoreServiceProvider as the event-bus map grows.
 */
class WebhookDispatcher implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'webhooks';

    public function handle(object $event): void
    {
        $companyId = $event->company_id ?? null;

        if (! is_string($companyId)) {
            return;
        }

        $eventType = class_basename($event);

        $endpoints = WebhookEndpoint::query()->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereJsonContains('events', $eventType)
            ->get();

        $payload = [
            'event' => $eventType,
            'company_id' => $companyId,
            'occurred_at' => now()->toIso8601String(),
            'data' => collect(get_object_vars($event))->except('socket')->all(),
        ];

        foreach ($endpoints as $endpoint) {
            $delivery = WebhookDelivery::query()->withoutGlobalScopes()->create([
                'endpoint_id' => $endpoint->id,
                'company_id' => $companyId,
                'event_type' => $eventType,
                'payload' => $payload,
            ]);

            DeliverWebhookJob::dispatch($endpoint->id, $eventType, $payload, $delivery->id);
        }
    }
}
