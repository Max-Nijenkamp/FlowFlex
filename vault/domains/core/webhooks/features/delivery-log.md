---
domain: core
module: webhooks
feature: delivery-log
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Delivery Log

Parent: [[../_module]] · See [[../architecture]]

An audit trail of every delivery attempt.

- Each attempt writes a `webhook_deliveries` row: `event_type`, `payload` (as sent), `response_status`, `attempts`, `delivered_at`.
- Surfaced as a relation manager on `WebhookEndpointResource` — timestamp, event type, response status, retry count.
- `PruneWebhookDeliveriesCommand` (daily, date-guarded) deletes rows older than 30 days *(assumed)*.
- Indexed `(company_id, delivered_at)` for tenant-scoped log queries.

## UI

- **Kind**: simple-resource
- **Page**: a relation manager on `WebhookEndpointResource` at `/app/webhook-endpoints` (per-endpoint deliveries table).
- **Layout**: table of attempts — timestamp, event type, response status (colour-coded 2xx/non-2xx), retry count; row → payload/response detail.
- **Key interactions**: user opens an endpoint, reviews recent deliveries, inspects a failed payload to debug the receiver; read-only (no edit).
- **States**: empty = "No deliveries yet" for a new endpoint · loading = table skeleton · error = log query failure placeholder · selected = a delivery row expanded to show payload + response.
- **Gating**: `core.webhooks.view-any` (+ `BillingService::hasModule('core.webhooks')`).

## Data

- Owns / writes: `webhook_deliveries` (one row per attempt: `event_type`, `payload`, `response_status`, `attempts`, `delivered_at`). `PruneWebhookDeliveriesCommand` deletes rows >30 days *(assumed)*.
- Reads: only its own delivery rows, tenant-scoped.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: delivery results from `DeliverWebhookJob` (own module).
- Feeds: none (audit surface; not consumed by other domains).
- Shared entity: `webhook_deliveries` (owned here).

## Test Checklist

### Unit
- [ ] Prune date-guard selects only rows older than 30 days *(assumed)*

### Feature (Pest)
- [ ] Each delivery attempt writes one `webhook_deliveries` row (`event_type`, `payload`, `response_status`, `attempts`, `delivered_at`)
- [ ] Log queries are tenant-scoped (`company_id, delivered_at` index); company A never sees company B deliveries
- [ ] `PruneWebhookDeliveriesCommand` removes rows >30 days and leaves newer ones

### Livewire
- [ ] Deliveries relation manager is read-only (no edit action) and requires `core.webhooks.view-any`
- [ ] A failed row expands to show the payload + response for debugging
