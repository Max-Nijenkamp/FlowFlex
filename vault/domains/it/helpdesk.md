---
type: module
domain: IT & Security
panel: it
module-key: it.helpdesk
status: planned
color: "#4ADE80"
---

# IT Helpdesk

Internal IT support ticket system for employees. Report issues, request hardware/software, track resolution. Like the Support domain but internal-facing.

## Core Features

- IT ticket: title, description, requester (employee), category, priority, assignee (IT staff), status
- Categories: hardware, software, access, network, account
- Status machine: `open → in_progress → resolved → closed`
- Request types: incident (something broken) vs service request (need something)
- Linked to assets (e.g. "my laptop won't boot" → links to the asset)
- Internal knowledge base for common fixes
- SLA targets per priority
- Assignment to IT team members

## Data Model

| Table | Key Columns |
|---|---|
| `it_tickets` | company_id, ticket_number, title, description, requester_employee_id, category, priority, status, assignee_id, asset_id, resolved_at |
| `it_ticket_replies` | ticket_id, company_id, author_id, body, is_internal |

## Filament

**Nav group:** Helpdesk

- `ItTicketResource` — list, create, assign, resolve
- `ItHelpdeskQueuePage` (custom page) — IT staff queue view
- Employee self-service ticket creation

## Related

- [[domains/it/asset-inventory]]
- [[domains/support/tickets]] — similar pattern
- [[domains/hr/employee-profiles]]
