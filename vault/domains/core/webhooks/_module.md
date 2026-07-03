---
domain: core
module: webhooks
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Webhooks

Configurable outbound webhooks on domain events. Companies register endpoints, subscribe to events (employee hired, invoice paid, deal won), and receive signed HTTP POST payloads. A universal dispatcher listens to the event-bus map generically and delivers via a retrying queued job.

## Module-key

`core.webhooks`

**Priority:** v1  
**Panel:** app  
**Permission prefix:** `core.webhooks`  
**Tables:** `webhook_endpoints`, `webhook_deliveries`  
**Events:** fires none · consumes none as fixed contracts — the `WebhookDispatcher` listens to the whole [[../../../architecture/event-bus]] map generically

## Sibling notes

- [[architecture]] — dispatcher, delivery job, backoff + flow
- [[data-model]] — `webhook_endpoints`, `webhook_deliveries` + ERD
- [[api]] — `CreateWebhookEndpointData` DTO
- [[security]] — HMAC signing, encrypted secret, https-only, tenant isolation
- [[unknowns]] — `*(assumed)*` markers
- Features: [[features/endpoint-management]] · [[features/signed-delivery]] · [[features/retry-backoff]] · [[features/delivery-log]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.queues | deliveries on the `webhooks` queue |
| Hard | [[../billing-engine/_module]] | module gating |
| Hard | [[../rbac/_module]] | permissions |

Subscribable events come from whichever domain modules are built — the dispatcher listens to the [[../../../architecture/event-bus]] map generically.

## Core Features

- Endpoint management: URL, secret, active events, enabled/disabled
- Event selection grouped by domain — only events of active modules are subscribable
- Signed payloads: `X-FlowFlex-Signature` header = HMAC-SHA256 of payload + secret
- Delivery log: timestamp, event type, response status, retry count
- Automatic retries: 3 retries with exponential backoff on non-2xx
- Test button: send a test payload to verify reachability
- Endpoint auto-disabled after 20 consecutive failures *(assumed)*, owner notified

## Test Checklist

- [ ] Tenant isolation: company A events never delivered to company B endpoints
- [ ] Module gating: resource gated; inactive-module events not subscribable
- [ ] Signature header verifies with `hash_equals` against the secret
- [ ] Non-2xx response retries with backoff, then recorded failed
- [ ] 20 consecutive failures auto-disable + notify
- [ ] Secret displayed once, stored encrypted, rotate works (rotate holds a lock — no double-rotate)
- [ ] Non-HTTPS URL rejected
- [ ] `test` and `rotate` actions carry their own permission + `panel-action` rate limiter

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_webhook_endpoints_table.php
database/migrations/xxxx_create_webhook_deliveries_table.php
app/Models/{WebhookEndpoint,WebhookDelivery}.php
app/Data/CreateWebhookEndpointData.php
app/Listeners/WebhookDispatcher.php
app/Jobs/DeliverWebhookJob.php
app/Actions/{SendTestWebhookAction,RotateWebhookSecretAction}.php
app/Console/Commands/PruneWebhookDeliveriesCommand.php
app/Filament/App/Resources/WebhookEndpointResource.php
database/factories/{WebhookEndpointFactory,WebhookDeliveryFactory}.php
tests/Feature/Core/{WebhookDeliveryTest,WebhookSecurityTest}.php
```

> [!note]
> Spec listed `app/Models/Core/...`, `app/Data/Core/...`, `app/Listeners/Core/...`, `app/Jobs/Core/...`, `app/Actions/Core/...`, `app/Console/Commands/Core/...`, and `database/factories/Core/...`; real layout is flat — corrected above. `app/Filament/...` and `tests/Feature/Core/...` kept as-is.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| consumes | *(every event in the event-bus map, generically)* | all domains | `WebhookDispatcher` matches active endpoints and dispatches `DeliverWebhookJob` per endpoint |
| fires | none | — | delivery is an outbound HTTP POST to an external URL, not an internal domain event |

Data ownership: webhooks owns and writes only `webhook_endpoints` and `webhook_deliveries`; it reads every domain's event payloads (read-only, as-sent) and the active-module set via `BillingService::hasModule` (read-only), and effects other domains via no events — deliveries leave FlowFlex over HTTP ([[../../../security/data-ownership]]).

## Related

- [[../../../security/webhooks-signing]] — HMAC signature contract
- [[../../../architecture/event-bus]] — subscribable events
- [[../../../architecture/patterns/encryption]] — secret storage
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]] · [[../../../security/data-ownership]]
- [[../api-clients/_module]]
