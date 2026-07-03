---
domain: it
module: helpdesk
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# IT Helpdesk — Architecture

See also [[_module|helpdesk._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/states]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/patterns/actions-pattern]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

Simple, single-step operations — implemented as `lorisleiva/laravel-actions` classes, not a multi-method service ([[../../../architecture/patterns/actions-pattern]]).

- `CreateItTicketAction::run(CreateItTicketData $data): ItTicket` — assigns sequential `ticket_number`, sets `status = open`, requester = current employee.
- `AssignItTicketAction::run(ItTicket $ticket, User $assignee): void` — sets `assignee_id`, transitions `open → in_progress` on first assignment.
- `ReplyAction::run(ItTicket $ticket, ItReplyData $data): ItTicketReply` — appends a reply; if `is_internal = false`, notifies the requester via core.notifications; internal replies notify no one.
- `ResolveItTicketAction::run(ItTicket $ticket): void` — transitions `in_progress → resolved`, stamps `resolved_at`, notifies requester.

**Requester scope rule:** employees see and reply to their own tickets only. IT staff holding `it.helpdesk.respond` see and act on all company tickets. Enforced in the resource query / action guards, not just the UI ([[security|helpdesk.security]]).

---

## State Machine

`spatie/laravel-model-states` on `ItTicket::status` ([[../../../architecture/patterns/states]]).

```
open → in_progress → resolved → closed
```

- `open` — created, unassigned (or reopened).
- `in_progress` — assigned to IT staff / actively worked.
- `resolved` — IT marked fixed; `resolved_at` stamped; requester notified.
- `closed` — terminal; `closed_at` stamped. Auto-close 3 days after `resolved_at` *(assumed)* — see [[decisions|helpdesk.decisions]].
- Reopen (`resolved → in_progress`) allowed if the requester replies before auto-close *(assumed)* — see [[unknowns|helpdesk.unknowns]].

---

## Jobs & Scheduling

- `AutoCloseItTicketsCommand` — scheduled command (daily *(assumed)*) that transitions `resolved` tickets whose `resolved_at` is older than 3 days to `closed`. Registered in the console kernel schedule. This is the sole path that produces `closed` automatically.

---

## Filament Artifacts

**Nav group:** Helpdesk

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ItTicketResource` | #1 CRUD resource | tweaks: state-badge-column, relation-manager-timeline (replies thread), custom-header-actions (assign / resolve) | My tickets / All (permission) tabs; category + priority + status columns/filters |
| `ItHelpdeskQueuePage` | #8 shared-inbox / work-queue custom page | [[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] | IT staff queue, priority-sorted; **polling 30s** (no Reverb for internal helpdesk *(assumed)*) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('it.helpdesk.view-any') && BillingService::hasModule('it.helpdesk')`
per [[../../../architecture/filament-patterns]] #1. `ItHelpdeskQueuePage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. Self-service creation reaches the create form / own
tickets via `it.helpdesk.create-own` without `view-any`. No public/portal surface (internal-facing). See
[[security|helpdesk.security]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Ticket CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Ticket-number allocation (create) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the per-company counter so `ticket_number` stays sequential and gap-free *(assumed)* |
| Status transition (assign / resolve / reopen) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read status, write per [[../../../architecture/patterns/states]] |
| Reply append | n-a | append-only insert into `it_ticket_replies`; no concurrent update on the same row |
| Auto-close (scheduled command) | n-a | schedule-driven single writer; guarded by `resolved_at` age (>3d) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].
