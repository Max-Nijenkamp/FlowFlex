---
domain: communications
module: sms-channel
feature: cost-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Cost Tracking

Records the per-message SMS cost reported by the provider, so spend is visible (and feeds comms analytics).

## Behaviour

- Provider delivery callback carries a price; the driver/webhook records it as `cost_cents` in `comms_messages.meta` *(assumed jsonb meta column)* using `brick/money`.
- Cost is read-only reporting; no billing charge in this module.

## UI

- **Kind**: background (write on callback) — cost surfaces read-only in message detail + [[../../comms-analytics/_module|Comms Analytics]].
- **Trigger**: provider status callback (webhook).
- **Gating**: signature-gated webhook; viewing under `comms.sms.manage` / `comms.analytics.view`.

## Data

- Owns / writes: nothing of its own — cost rides in the inbox-owned `comms_messages.meta`. This module supplies the value; the inbox writes the row *(assumed — see [[../unknowns]])*.
- Reads: provider callback.
- Cross-domain writes: none — never writes `comms_messages` directly ([[../../../security/data-ownership]]).

## Relations

- Consumes: provider status callback.
- Feeds: cost data read by [[../../comms-analytics/_module|comms.analytics]].
- Shared entity: `comms_messages.meta` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Related

- [[../_module|SMS Channel]] · [[outbound-send]] · [[../unknowns]]
