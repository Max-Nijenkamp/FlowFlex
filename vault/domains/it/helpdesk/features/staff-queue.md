---
domain: it
module: helpdesk
feature: staff-queue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Staff Queue

The IT-staff triage board — a priority-sorted queue of all open/in-progress tickets, polling for near-real-time updates. This is the day-to-day workspace for the IT team, distinct from the plain CRUD resource ([[ticket-management]]).

## Behaviour

- Shows all company tickets not yet `closed`, sorted by priority (urgent → low) then age.
- IT staff pick up, assign, reply and resolve directly from the queue.
- Polls every 30s so newly created / reassigned tickets surface without a manual refresh.
- Requester-scope does **not** apply here — this page is staff-only and shows every ticket in the company.

## UI

- **Kind**: custom-page — `ItHelpdeskQueuePage` (Livewire), #8-style approval/work queue ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: "Helpdesk queue" (`/it/helpdesk/queue`), nav group **Helpdesk**.
- **Layout**: priority-grouped list/table of open + in-progress tickets; each row shows ticket_number, title, requester, category, priority badge, age, assignee; row actions to assign / reply / resolve.
- **Key interactions**: assign to me / to a teammate · quick reply · resolve · filter by category/assignee. Polling refresh every 30s.
- **States**: empty (no open tickets → "Queue clear" state) · loading (skeleton rows) · error (toast + retry) · selected (row expands to detail / slide-over with reply thread).
- **Gating**: `canAccess()` requires `it.helpdesk.view-any` **and** `BillingService::hasModule('it.helpdesk')`, stated explicitly on the page ([[../security|helpdesk.security]], [[../../../../architecture/filament-patterns]] #1).

## Data

- Owns / writes: `it_tickets` (status/assignee), `it_ticket_replies` (replies from the queue).
- Reads: `hr_employees` (requester display), `it_assets` (linked asset badge).
- Cross-domain writes: none — resolve/reply notifications route through core.notifications ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (reads its own tickets + employee/asset display data).
- Feeds: resolve/assign/public-reply → requester notification via core.notifications.
- Shared entity: requester = `hr_employees` (hr.profiles); `asset_id` = `it_assets` (it.assets).

## Unknowns

- `*(assumed)*` polling interval 30s (no Reverb/websocket realtime planned for internal helpdesk) — see [[../unknowns|helpdesk.unknowns]].
- Whether the queue also lists `resolved` (awaiting auto-close) tickets or only open/in-progress is unconfirmed.

## Related

- [[../_module|IT Helpdesk]] · [[ticket-management]] · [[replies-thread]] · [[../../../../architecture/patterns/custom-pages]] · [[../../../../architecture/ui-strategy]]
