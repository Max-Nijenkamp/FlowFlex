---
domain: core
module: webhooks
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Webhooks — Unknowns / UNVERIFIED

Parent: [[_module]]

## `*(assumed)*` markers carried from spec

- `webhook_endpoints.url` is **https-only** *(assumed)* — enforced in `CreateWebhookEndpointData`.
- Endpoint auto-disabled after **20 consecutive failures** *(assumed)*, owner notified.
- `webhook_deliveries` pruned after **30 days** *(assumed)* by `PruneWebhookDeliveriesCommand`.
- Consumer-side dedupe via a payload `id` field *(assumed)* — the delivery row is keyed per (endpoint, event instance) on our side.

> [!warning] UNVERIFIED — needs confirmation
> App source is not checked out; the above thresholds (20 failures, 30-day prune, https-only) come from the spec's `*(assumed)*` markers and have not been confirmed against `app/`.
