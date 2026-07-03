---
domain: support
module: tickets
feature: ticket-lifecycle
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Ticket Lifecycle

The status machine every ticket moves through, plus reply threading and resolution.

## Behaviour

- States: `open → in_progress → waiting_on_customer → resolved → closed`, with reopen back to `open`.
- Transitions (guards + side effects) per [[../architecture|architecture]] State Machine table: SLA pause on `waiting_on_customer`, resume on customer reply; `resolved` fires `TicketResolved` + stamps `resolved_at`; auto-close `resolved` after 3 days *(assumed)*.
- Replies: public reply emails the requester + stamps `first_response_at` (first agent public reply only); internal note is silent, never emailed.
- Reopen within a configurable window (default 14 days *(assumed)*).

## UI

- **Kind**: simple-resource — `TicketResource` list + detail/edit form with status transitions as row/detail actions.
- **Page**: `TicketResource` (`/support/tickets`) + view page.
- **Layout**: table (number, subject, requester, status badge, priority, assignee, updated); detail = description + threaded replies + status/priority/assignee side panel.
- **Key interactions**: status transition action (guarded, confirm) → `TicketService` transition; reply composer (public vs internal-note toggle) → `TicketService::reply`; resolve action → fires `TicketResolved`.
- **States**: empty (no tickets → "no tickets yet" CTA) · loading (table skeleton) · error (invalid transition rejected, toast) · selected (open ticket highlighted, detail panel).
- **Gating**: view `support.tickets.view-any`; reply `support.tickets.reply`; resolve `support.tickets.resolve`; assign `support.tickets.assign`.

## Data

- Owns / writes: `sup_tickets`, `sup_ticket_replies` (status, stamps, reply rows).
- Reads: `crm_contacts` via `ContactService` (requester link, soft); `sup_sla_policies` (timer target, read).
- Cross-domain writes: none directly — fires `TicketResolved`; analytics/marketing react on their own tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: customer inbound reply (webhook) → `waiting_on_customer → in_progress` + SLA resume.
- Feeds: `TicketResolved` → [[../../support-analytics/_module|support.analytics]] (CSAT), marketing CSAT (P3).
- Shared entity: `crm_contacts` (requester) owned by [[../../../crm/contacts/_module|crm.contacts]].

## Test Checklist

### Unit
- [ ] Reopen-window guard: a `closed` ticket older than the window (14d *(assumed)*) rejects reopen
- [ ] `first_response_at` stamps only on the first agent public reply, never on an internal note

### Feature (Pest)
- [ ] Resolve fires `TicketResolved` with the contract payload and stamps `resolved_at`
- [ ] Public reply queues requester mail; internal note is silent (no mail)
- [ ] Customer inbound reply flips `waiting_on_customer → in_progress` and resumes the SLA clock
- [ ] Invalid transition throws (e.g. `closed → resolved`); concurrent double-resolve rejected via row lock
- [ ] Tenant isolation: company A cannot transition or reply to a company B ticket

### Livewire
- [ ] Status transition action is guarded + confirmed; invalid transition shows an error toast
- [ ] Reply composer public/internal toggle routes to `TicketService::reply`; resolve denied without `support.tickets.resolve`

## Unknowns

- `waiting_on_customer` explicit-toggle vs automatic *(assumed)*; reopen/auto-close windows should be settings — see [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[./email-to-ticket]] · [[./ticket-merge]] · [[../../sla/_module|support.sla]]
