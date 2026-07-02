---
domain: support
module: tickets
feature: ticket-inbox
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Ticket Inbox

A collaborative, real-time, email-client-style queue where agents triage and reply to tickets.

## Behaviour

- Three-panel layout: filters/folders (left), ticket list (middle), conversation + reply composer (right).
- Reverb broadcasts new tickets and replies to all agents viewing the inbox (`company.{id}.support`) so the queue updates live without refresh.
- Reply composer supports public reply / internal note toggle, canned-response insertion (soft-dep on [[../../canned-responses/_module|support.canned]]), and attachments.
- Agent suggestions: KB article suggestions surface from ticket subject (soft-dep on [[../../knowledge-base/_module|support.kb]]).

## UI

- **Kind**: custom-page — bespoke three-pane layout + realtime; not table+form.
- **Page**: "Ticket Inbox" (`/support/inbox`) — Filament custom Page (Livewire) + Reverb, ui-strategy row #8.
- **Layout**: left rail = status/priority/assignee/category filters; middle = live ticket list; right = conversation thread + composer.
- **Key interactions**: click ticket → load conversation; type reply → send (optimistic append + broadcast); new ticket arrives → toast + list prepend; assign/resolve inline.
- **States**: empty (no tickets in filter → "inbox zero" illustration) · loading (skeleton list) · error (send fails → retry toast) · selected (active ticket highlighted, thread loaded).
- **Gating**: `support.tickets.view-any`; reply `support.tickets.reply`.

## Data

- Owns / writes: `sup_tickets`, `sup_ticket_replies`.
- Reads: `crm_contacts` (requester context) via `ContactService`; canned responses + KB suggestions via their services (soft).
- Cross-domain writes: none — broadcasts + fires `TicketResolved` only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: Reverb inbox events (same-domain broadcast).
- Feeds: `TicketResolved` on resolve.
- Shared entity: `crm_contacts` (requester).

## Unknowns

- Assignment auto-distribution in the inbox is manual until [[../../automations/_module|support.automations]] is active.

## Related

- [[../_module|Tickets]] · [[./ticket-lifecycle]] · [[../../../../architecture/websockets]]
