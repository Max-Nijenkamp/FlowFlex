---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: in-progress
migration_range: 250007–250008
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Customer Support & Helpdesk

Customer-facing ticket management with assignment, SLA tracking, and internal comments. Replaces Zendesk, Freshdesk, Intercom (support).

**Panel:** `crm`  
**Phase:** 3  
**Module key:** `crm.tickets`

---

## Data Model

```erDiagram
    crm_tickets {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        ulid assigned_to FK
        string number
        string title
        text description
        string status
        string priority
        string source
        timestamp resolved_at
        timestamp closed_at
        timestamp first_response_at
    }

    crm_ticket_comments {
        ulid id PK
        ulid ticket_id FK
        ulid created_by FK
        text body
        boolean is_internal
        timestamp created_at
    }
```

**Ticket status:** `open` | `in_progress` | `resolved` | `closed`

**Ticket priority:** `low` | `medium` | `high` | `urgent`

**Ticket source:** `email` | `phone` | `chat` | `portal` | `manual`

---

## Features

- Ticket queue: filterable by status, priority, assignee, and source
- Assignment: manual assign to agent or team; round-robin auto-assignment (Phase 5)
- Internal comments: notes visible only to agents, not the customer
- Public comments: customer reply simulation (Phase 5 — requires email integration)
- SLA tracking: first response time and resolution time targets per priority
- Ticket number: auto-generated (TKT-2026-00001)
- Tags for categorisation

---

## Permissions

```
crm.tickets.view
crm.tickets.create
crm.tickets.edit
crm.tickets.assign
crm.tickets.resolve
crm.tickets.delete
crm.tickets.view-internal-comments
```

---

## Related

- [[MOC_CRM]]
- [[contact-company-management]] — tickets linked to contacts
- [[shared-inbox]] — Phase 5 email thread ingestion feeds tickets
- [[MOC_IT]] — IT helpdesk has a parallel module (itsm-helpdesk) for internal IT tickets
