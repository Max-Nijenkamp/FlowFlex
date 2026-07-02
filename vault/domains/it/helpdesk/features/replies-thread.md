---
domain: it
module: helpdesk
feature: replies-thread
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Replies Thread

The conversation on a ticket — a chronological thread of replies between the requester and IT staff, with an `is_internal` flag for IT-only notes that the requester never sees.

## Behaviour

- Each reply: author, body, `is_internal` flag, timestamp — appended via `ReplyAction`.
- **Public reply** (`is_internal = false`): visible to the requester and notifies them via core.notifications.
- **Internal note** (`is_internal = true`): visible only to IT staff, generates **no notification** — IT-to-IT scratchpad on the same thread.
- Only IT staff (`it.helpdesk.respond`) may set `is_internal = true` *(assumed)*; requester replies are always public.
- A requester replying to a `resolved` ticket may reopen it before auto-close *(assumed)* ([[../architecture|helpdesk.architecture]]).

## UI

- **Kind**: simple-resource (relation) — a `RepliesRelationManager` / thread panel on the `ItTicketResource` infolist, not a standalone page.
- **Page**: rendered inside the ticket detail (`/it/helpdesk/tickets/{ticket}` and the queue slide-over).
- **Layout**: chronological reply list; internal notes visually distinguished (e.g. amber/"internal" badge) and hidden from the requester's rendering; composer with body field + `is_internal` toggle (toggle shown to staff only).
- **Key interactions**: post public reply (notifies requester) · post internal note (staff only, silent) · requester public reply (may reopen).
- **States**: empty (no replies → "No replies yet") · loading (skeleton) · error (validation toast) · selected (composer focused).
- **Gating**: reply on own ticket via `it.helpdesk.create-own`; reply on any ticket + internal-note toggle via `it.helpdesk.respond`. Requester rendering filters out `is_internal = true` rows ([[../security|helpdesk.security]]).

## Data

- Owns / writes: `it_ticket_replies` (and may transition the parent `it_tickets.status` on reopen).
- Reads: `hr_employees`/`users` (author display).
- Cross-domain writes: none — public replies fire a requester notification via core.notifications, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (reads author display data).
- Feeds: public reply → requester notification (core.notifications); reply visible in [[staff-queue]] + [[ticket-management]] detail.
- Shared entity: author references `users` / `hr_employees`; thread hangs off `it_tickets` (owned here).

## Unknowns

- `*(assumed)*` only `it.helpdesk.respond` holders may mark a reply internal — see [[../unknowns|helpdesk.unknowns]].
- `*(assumed)*` requester reply reopens a resolved ticket; exact trigger + whether it resets the auto-close countdown unconfirmed.

## Related

- [[../_module|IT Helpdesk]] · [[ticket-management]] · [[staff-queue]] · [[self-service-requests]]
