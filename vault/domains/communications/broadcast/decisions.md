---
domain: communications
module: broadcast
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast — Decisions

## ADR: Recipients snapshotted at schedule time (source)

- **Context:** Audiences (segments, groups) change over time.
- **Decision:** `BroadcastService::schedule` materialises the recipient list (address + name snapshot) into `comms_broadcast_recipients` at schedule time — not at send time. Dedupe on `(broadcast_id, address)`.
- **Consequences:** Deterministic sends; opt-outs + undeliverables excluded once, up front.

## ADR: Resume-safe batched send (source)

- **Decision:** `SendBroadcastBatchJob` is chained + chunked, per-recipient try/catch, guarded on recipient `pending`. A mid-send failure resumes only pending recipients.
- **Consequences:** No double-sends on retry; `sending → failed` is resumable.

## ADR: Audiences are read-only from CRM/HR (data-ownership)

- **Decision:** Customer audiences come from `SegmentService::contacts()`; employee audiences from HR profiles — both **read-only**. Broadcast writes only its own snapshot.
- **Consequences:** No write into CRM/HR tables ([[../../../security/data-ownership]]).

## ADR: Email always available; other channels degrade (source)

- **Decision:** Manual list + email work without any channel module; WhatsApp/SMS/in-app appear only when their modules are active.

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/patterns/states]]
