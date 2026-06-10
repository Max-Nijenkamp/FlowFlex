---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.webhooks
status: planned
priority: v1
depends-on: [foundation.queues, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [queues, encryption]
tables: [webhook_endpoints, webhook_deliveries]
permission-prefix: core.webhooks
encrypted-fields: ["webhook_endpoints.secret"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Webhooks

Configurable outbound webhooks on domain events. Companies subscribe to events (employee hired, invoice paid, deal won) and receive HTTP POST payloads at their own endpoints.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | deliveries on `webhooks` queue |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Subscribable events come from whichever domain modules are built — the dispatcher listens to the event-bus map generically.)

---

## Core Features

- Webhook endpoint management: URL, secret, active events, enabled/disabled
- Event selection: grouped by domain — subscribe to any combination from the [[architecture/event-bus]] map (only events of active modules shown)
- Signed payloads: `X-FlowFlex-Signature` header with HMAC-SHA256 of payload + secret
- Delivery log: timestamp, event type, response status, retry count
- Automatic retries: 3 retries with exponential backoff on non-2xx responses
- Test button: send a test payload to verify endpoint is reachable
- Endpoint auto-disabled after 20 consecutive failures *(assumed)*, owner notified

---

## Data Model

### webhook_endpoints

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | |
| url | string | not null, https only *(assumed)* | |
| 🔐 secret | text | not null | encrypted cast; shown once at creation |
| events | jsonb | not null | event class names |
| is_active | boolean | default true | |
| consecutive_failures | int | default 0 | auto-disable counter |
| deleted_at | timestamp | nullable | |

### webhook_deliveries

| Column | Type | Notes |
|---|---|---|
| id, endpoint_id FK, company_id | ulid | indexed `(company_id, delivered_at)` |
| event_type | string | |
| payload | jsonb | as sent |
| response_status | int nullable | null = no response |
| attempts | int | |
| delivered_at | timestamp nullable | success time |
| created_at | timestamp | |

Deliveries pruned after 30 days *(assumed)*.

---

## DTOs

### CreateWebhookEndpointData (input)
| Field | Type | Validation |
|---|---|---|
| url | string | required, url, starts with https:// |
| events | array<string> | required, each in event-bus map, each module active |

Message: "Webhook URLs must use HTTPS."

## Services & Actions

- `WebhookDispatcher` — universal queued listener registered for every event in the map: finds active endpoints subscribed to the event for `company_id`, dispatches one `DeliverWebhookJob` per endpoint
- `DeliverWebhookJob` (`webhooks` queue, tries 4, backoff [30, 120, 600]) — signs, POSTs (10s timeout), records delivery, increments/resets failure counter
- `SendTestWebhookAction::run(string $endpointId): WebhookDelivery`
- `RotateWebhookSecretAction::run(string $endpointId): string` — returns new plain secret once

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WebhookEndpointResource` | #1 CRUD resource | event checkboxes grouped by domain; delivery log relation manager; test + rotate-secret actions |

---

## Permissions

`core.webhooks.view-any` · `core.webhooks.create` · `core.webhooks.update` · `core.webhooks.delete` · `core.webhooks.test`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DeliverWebhookJob` | webhooks | on event | delivery row keyed per (endpoint, event instance); consumer-side dedupe via payload `id` field *(assumed)* |
| `PruneWebhookDeliveriesCommand` | default | daily | date-guard delete |

---

## Test Checklist

- [ ] Tenant isolation: company A events never delivered to company B endpoints
- [ ] Module gating: resource gated; inactive-module events not subscribable
- [ ] Signature header verifies with `hash_equals` against the secret
- [ ] Non-2xx response retries with backoff, then recorded failed
- [ ] 20 consecutive failures auto-disable + notify
- [ ] Secret displayed once, stored encrypted, rotate works
- [ ] Non-HTTPS URL rejected

---

## Build Manifest

```
database/migrations/xxxx_create_webhook_endpoints_table.php
database/migrations/xxxx_create_webhook_deliveries_table.php
app/Models/Core/{WebhookEndpoint,WebhookDelivery}.php
app/Data/Core/CreateWebhookEndpointData.php
app/Listeners/Core/WebhookDispatcher.php
app/Jobs/Core/DeliverWebhookJob.php
app/Actions/Core/{SendTestWebhookAction,RotateWebhookSecretAction}.php
app/Console/Commands/Core/PruneWebhookDeliveriesCommand.php
app/Filament/App/Resources/WebhookEndpointResource.php
database/factories/Core/{WebhookEndpointFactory,WebhookDeliveryFactory}.php
tests/Feature/Core/{WebhookDeliveryTest,WebhookSecurityTest}.php
```

---

## Related

- [[architecture/event-bus]] — subscribable events
- [[domains/core/api-clients]]
- [[architecture/patterns/encryption]] — secret storage
