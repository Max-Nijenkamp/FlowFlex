---
domain: it
module: helpdesk
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# IT Helpdesk

Internal IT support ticket system for employees. Report issues, request hardware/software, track resolution. Same pattern as [[../../support/tickets/_module|support.tickets]] but internal-facing (no email-to-ticket, no external requesters). Owns `it_tickets` + `it_ticket_replies`.

---

## Module-key

`it.helpdesk`

**Priority:** p3  
**Panel:** it  
**Permission prefix:** `it.helpdesk`  
**Tables:** `it_tickets`, `it_ticket_replies`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | requesters are employees |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, status notifications |
| Soft | [[../asset-inventory/_module\|it.assets]] | ticket ↔ asset link (read/FK only) |

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see/reply to company B tickets
- [ ] Module gating: artifacts hidden when `it.helpdesk` inactive
- [ ] Requester sees own tickets only; IT sees all
- [ ] Internal reply invisible to requester, no notification
- [ ] Asset link restricted to requester's assigned assets
- [ ] Resolve → auto-close after 3d (command)
- [ ] Ticket numbers sequential

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `hr_employees` | hr.profiles | requester is an employee; ticket displays requester name/dept (read only) |
| Reads | `it_assets` | it.assets | soft asset link on a ticket (`asset_id` FK/read; degrades to no-link if module off) |
| Fires (via) | status-change notifications | core.notifications | resolve/reply notify the requester through the notifications module |

**Data ownership:** `it.helpdesk` writes only `it_tickets` + `it_ticket_replies`; the soft asset link is a read/FK to `it.assets` (never writes it), and requester data is read from `hr.profiles`. Helpdesk consumes no HR events directly — it is internal-facing and simply reads employee data at display time. All cross-domain effects go through owning-service APIs / notifications ([[../../../security/data-ownership]]).

---

## Related

- [[../asset-inventory/_module|it.assets]]
- [[../../support/tickets/_module|support.tickets]] — external-facing sibling
- [[architecture|helpdesk.architecture]]
- [[data-model|helpdesk.data-model]]
- [[security|helpdesk.security]]
- [[decisions|helpdesk.decisions]]
- [[unknowns|helpdesk.unknowns]]
- [[features/ticket-management|ticket-management feature]]
- [[features/staff-queue|staff-queue feature]]
- [[features/self-service-requests|self-service-requests feature]]
- [[features/replies-thread|replies-thread feature]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/patterns/states]]
- [[../../../architecture/ui-strategy]]
