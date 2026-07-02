---
domain: communications
module: shared-inbox
feature: snooze-reopen
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Snooze & Reopen

Snooze a conversation until later; it auto-reopens on schedule or immediately when the customer replies.

## Behaviour

- `snooze(conversationId, until)` sets `snoozed_until` and status → `snoozed`; the conversation drops out of the open list.
- `ReopenSnoozedCommand` runs every 15 min: any conversation with `snoozed_until <= now` returns to `open`.
- An inbound message on a snoozed conversation reopens it **immediately** (handled in `handleInbound`).

## UI

- **Kind**: widget (snooze action + duration picker on the [[unified-conversation-view]] thread header).
- **Key interactions**: click snooze → pick "until" → conversation hidden; auto-returns on time or on inbound.
- **States**: default (snooze action visible) · snoozed (badge + hidden from open list) · reopened (back in open list).
- **Gating**: `comms.inbox.reply` *(assumed)*.

## Data

- Owns / writes: `comms_conversations.snoozed_until`, `status` (own module).
- Reads: nothing cross-domain.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: nothing cross-domain (status change is internal; live update via Reverb).
- Shared entity: none.

## Related

- [[../_module|Shared Inbox]] · [[../architecture]] · [[../../../architecture/queue-jobs]]
