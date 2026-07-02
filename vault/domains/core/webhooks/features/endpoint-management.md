---
domain: core
module: webhooks
feature: endpoint-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Endpoint Management

Parent: [[../_module]] · See [[../architecture]] · [[../api]] · [[../security]]

CRUD for outbound webhook endpoints: register a URL, pick which domain events to subscribe to, enable/disable, and send a test payload to verify reachability.

## Behaviour

- Create/edit an endpoint via `CreateWebhookEndpointData`: HTTPS-only `url`, an `events` array (each event must be in the event-bus map **and** belong to a currently-active module), enabled/disabled flag.
- Event selection is grouped by domain; only events of active modules are subscribable.
- Test button → `SendTestWebhookAction` sends a signed test payload (rate-limited per endpoint per minute) and records a delivery.
- Disabling an endpoint stops the dispatcher from matching it; endpoints auto-disable after 20 consecutive failures *(assumed)*.

## UI

- **Kind**: simple-resource
- **Page**: `WebhookEndpointResource` at `/app/webhook-endpoints` (list + create/edit).
- **Layout**: list table — URL, subscribed-event count, active badge, last delivery status. Create/edit form: URL field (HTTPS-validated), domain-grouped event checkboxes, active toggle, create-once secret reveal; a "Send test" row action and a deliveries relation manager ([[delivery-log]]).
- **Key interactions**: create endpoint (copy the one-time secret), pick events, save; toggle active; click "Send test" to verify; rotate secret ([[signed-delivery]]).
- **States**: empty = "No webhook endpoints" with a create CTA · loading = table/form skeleton · error = non-HTTPS URL rejected ("Webhook URLs must use HTTPS"), or an event from an inactive module blocked · selected = an endpoint open in the edit form.
- **Gating**: `core.webhooks.view-any` / `.create` / `.update` / `.delete` / `.test` (+ `BillingService::hasModule('core.webhooks')`).

## Data

- Owns / writes: `webhook_endpoints` (url, encrypted secret, events, is_active, consecutive_failures). Test sends write a `webhook_deliveries` row.
- Reads: the event-bus map and the company's active-module set (via `BillingService::hasModule`, read-only) to constrain subscribable events.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: none (config surface).
- Feeds: registered endpoints drive `DeliverWebhookJob` on any subscribed domain event ([[signed-delivery]], [[retry-backoff]]).
- Shared entity: the [[../../../architecture/event-bus]] map (read) and the active-module set owned by [[../billing-engine/_module]] (read-only).

## Unknowns

- HTTPS-only enforcement and the 20-failure auto-disable threshold are `*(assumed)*` in the source notes. See [[../unknowns]].

## Related

- [[../_module|Webhooks]] · [[signed-delivery]] · [[retry-backoff]] · [[delivery-log]]
