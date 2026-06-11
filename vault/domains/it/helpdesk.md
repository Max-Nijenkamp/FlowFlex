---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.helpdesk
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [it.assets]
fires-events: []
consumes-events: []
patterns: [states, custom-pages]
tables: [it_tickets, it_ticket_replies]
permission-prefix: it.helpdesk
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# IT Helpdesk

Internal IT support ticket system for employees. Report issues, request hardware/software, track resolution. Same pattern as [[domains/support/tickets|support.tickets]] but internal-facing (no email-to-ticket, no external requesters).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | requesters are employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, status notifications |
| Soft | [[domains/it/assets\|it.assets]] | ticket ↔ asset link |

---

## Core Features

- IT ticket: title, description, requester (employee), category, priority, assignee (IT staff), status
- Categories: hardware, software, access, network, account
- Status machine: `open → in_progress → resolved → closed` (auto-close 3d after resolve *(assumed)*)
- Request types: incident (broken) vs service request (need something)
- Linked to assets ("my laptop won't boot" → asset link)
- Internal replies thread (internal-only flag for IT notes)
- SLA targets per priority (simple per-priority hours config *(assumed: no full SLA module reuse)*)
- Assignment to IT team members
- Employee self-service ticket creation (visible to all users)

---

## Data Model

### it_tickets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| ticket_number | string | unique per company |
| title / description | string / text | |
| requester_employee_id | ulid FK | |
| category | string | in set |
| request_type | string | incident / service-request |
| priority | string | urgent/high/normal/low |
| status | string default `open` | state machine |
| assignee_id | ulid nullable FK users | |
| asset_id | ulid nullable | it.assets link |
| resolved_at / closed_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status, priority)`, `(company_id, assignee_id, status)`

### it_ticket_replies — id, ticket_id FK, company_id, author_id FK users, body, is_internal (bool), created_at

---

## DTOs

### CreateItTicketData — title (required), description (required), category (in set), request_type (in set), priority (default normal), asset_id? (own assigned asset for requesters *(assumed)*)
### ItReplyData — ticket_id, body (required), is_internal

## Services & Actions

Actions: `CreateItTicketAction`, `AssignItTicketAction`, `ReplyAction` (requester notified unless internal), `ResolveItTicketAction`.
**Requester scope**: employees see own tickets only; IT staff (`it.helpdesk.respond`) see all.

---

## Filament

**Nav group:** Helpdesk

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ItTicketResource` | #1 CRUD resource | My tickets / All (permission) tabs |
| `ItHelpdeskQueuePage` | #8-style queue custom page | IT staff queue, priority-sorted, polling 30s |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('it.helpdesk.view-any') && BillingService::hasModule('it.helpdesk')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`it.helpdesk.create-own` (all users) · `it.helpdesk.respond` · `it.helpdesk.assign` · `it.helpdesk.view-any`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Requester sees own tickets only; IT sees all
- [ ] Internal reply invisible to requester, no notification
- [ ] Asset link restricted to requester's assigned assets
- [ ] Resolve → auto-close after 3d (command)
- [ ] Ticket numbers sequential

---

## Build Manifest

```
database/migrations/xxxx_create_it_tickets_table.php
database/migrations/xxxx_create_it_ticket_replies_table.php
app/Models/IT/{ItTicket,ItTicketReply}.php
app/States/IT/ItTicket/{ItTicketState,Open,InProgress,Resolved,Closed}.php
app/Data/IT/{CreateItTicketData,ItReplyData}.php
app/Actions/IT/{CreateItTicketAction,AssignItTicketAction,ReplyAction,ResolveItTicketAction}.php
app/Console/Commands/IT/AutoCloseItTicketsCommand.php
app/Filament/IT/Resources/ItTicketResource.php
app/Filament/IT/Pages/ItHelpdeskQueuePage.php
database/factories/IT/ItTicketFactory.php
tests/Feature/IT/{ItHelpdeskTest,ItTicketScopeTest}.php
```

---

## Related

- [[domains/it/asset-inventory]]
- [[domains/support/tickets]] — external-facing sibling
