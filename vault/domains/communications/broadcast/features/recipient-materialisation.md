---
domain: communications
module: broadcast
feature: recipient-materialisation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Recipient Materialisation

At schedule time the audience is resolved into a fixed recipient snapshot — deduped, opt-outs and undeliverables excluded.

## Behaviour

- Segment audience → `SegmentService::contacts()` (read-only); employee-group → HR profile query; manual → the provided list.
- Each recipient becomes a `comms_broadcast_recipients` row (address + name snapshot, `status = pending`).
- Dedupe on `(broadcast_id, address)`; exclude SMS opt-outs (`OptOutService`) and `email_deliverable=false`.
- Snapshot is frozen — later audience changes don't affect this send.

## UI

- **Kind**: background
- **Trigger**: `BroadcastService::schedule` (on send/schedule). No dedicated screen; recipient count + exclusions shown on the `BroadcastResource` view page.
- **Gating**: runs under `comms.broadcast.send`.

## Data

- Owns / writes: `comms_broadcast_recipients` (own module).
- Reads: `crm.segments` (`SegmentService::contacts`), `hr.profiles` (employee groups), `comms.sms` (`OptOutService`) — all read-only.
- Cross-domain writes: none — audiences are read from those domains' services; only the snapshot is written here ([[../../../security/data-ownership]]).

## Relations

- Consumes: `SegmentService::contacts()` (CRM), employee-group query (HR), `OptOutService::isOptedOut` (SMS).
- Feeds: `SendBroadcastBatchJob` (reads pending recipients).
- Shared entity: `crm_contacts`, `hr_employees` (owned elsewhere, read-only).

## Related

- [[../_module|Broadcast]] · [[delivery-tracking]] · [[../../crm/customer-segments/_module|Segments]]
