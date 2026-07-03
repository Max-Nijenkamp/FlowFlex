---
domain: core
module: webhooks
feature: retry-backoff
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Retry & Backoff

Parent: [[../_module]] · See [[../architecture]]

Automatic retries with exponential backoff on delivery failure.

- `DeliverWebhookJob` runs on the `webhooks` queue with `tries = 4` and `backoff = [30, 120, 600]` seconds — 3 retries after the first attempt.
- Any non-2xx response or timeout triggers a retry; after the last attempt the delivery is recorded failed.
- Each failure increments `webhook_endpoints.consecutive_failures`; a success resets it to 0.
- After 20 consecutive failures *(assumed)* the endpoint is auto-disabled (`is_active = false`) and the owner is notified.

## UI

- **Kind**: background
- **Page**: background (no page) — `DeliverWebhookJob` on the `webhooks` queue. The failure count and disabled state surface on `WebhookEndpointResource` (`/app/webhook-endpoints`).
- **Layout**: none of its own; the endpoint row shows a `is_active` toggle and (once auto-disabled) a disabled badge; the delivery-log relation shows per-attempt retry counts.
- **Key interactions**: unattended retries; a user may re-enable an auto-disabled endpoint after fixing the receiver.
- **States**: empty = no failed deliveries · loading = n/a (queue) · error = each non-2xx/timeout increments `consecutive_failures` · selected = an auto-disabled endpoint pending re-enable.
- **Gating**: no permission on the job; re-enabling honours `core.webhooks.update`.

## Data

- Owns / writes: `webhook_endpoints.consecutive_failures` (increment/reset), `webhook_endpoints.is_active` (auto-disable), `webhook_deliveries` (attempt/result rows).
- Reads: only its own endpoint + delivery tables.
- Cross-domain writes: none. The owner-notify on auto-disable is delivered through the notifications infrastructure, not by writing the notifications table here. See [[../../../../security/data-ownership]].

## Relations

- Consumes: delivery outcome (2xx vs non-2xx/timeout) from the outbound POST.
- Feeds: auto-disable triggers an owner notification via [[../../notifications/_module]] (delivery infrastructure); no cross-domain server event emitted.
- Shared entity: `webhook_endpoints` counters (owned here).

## Test Checklist

### Unit
- [ ] Backoff schedule is `[30, 120, 600]` with `tries = 4` (3 retries after the first attempt)
- [ ] Non-2xx / timeout increments `consecutive_failures`; a 2xx resets it to 0

### Feature (Pest)
- [ ] A failing endpoint retries per backoff then records the delivery failed after the last attempt
- [ ] The 20th consecutive failure auto-disables the endpoint (`is_active = false`) and notifies the owner
- [ ] Concurrent deliveries incrementing `consecutive_failures` under lock do not double-count or race the auto-disable
