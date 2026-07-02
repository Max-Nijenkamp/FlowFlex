---
domain: communications
module: broadcast
feature: compose-schedule
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Compose & Schedule

Build a broadcast — pick channel + audience, write the body, preview, and send now or schedule.

## Behaviour

- Choose channel (email / whatsapp / sms / in-app) and audience (segment / employee-group / manual).
- Compose body with `{{first_name}}` personalisation; WhatsApp requires an approved template.
- Preview renders against a sample recipient.
- Send now → `scheduled → sending` immediately; or set `scheduled_at` for later (picked up by `DispatchScheduledBroadcastsCommand`).

## UI

- **Kind**: simple-resource
- **Page**: `BroadcastResource` (`/comms/broadcast`) — Broadcast nav group.
- **Layout**: table (title, channel, status, scheduled_at) + form (channel select → audience builder → composer → preview panel).
- **Key interactions**: build audience → compose → preview → "Send now" / "Schedule"; state badge tracks lifecycle.
- **States**: empty (no broadcasts → CTA) · loading (preview render) · error (WhatsApp needs approved template; no audience) · selected (view page shows funnel).
- **Gating**: `comms.broadcast.create`; send needs `comms.broadcast.send`.

## Data

- Owns / writes: `comms_broadcasts` (own module).
- Reads: active channels (inbox), approved templates (whatsapp) — read-only.
- Cross-domain writes: none — sends route through channel drivers ([[../../../security/data-ownership]]).

## Relations

- Consumes: channel availability + approved templates.
- Feeds: `BroadcastService::schedule` → recipient materialisation.
- Shared entity: `comms_whatsapp_templates` (owned by whatsapp, read-only).

## Related

- [[../_module|Broadcast]] · [[recipient-materialisation]] · [[../architecture]]
