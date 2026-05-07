---
tags: [flowflex, domain/it, helpdesk, support, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# Internal IT Helpdesk

Employee-facing portal for IT issues. Separate from the CRM customer support module — this is purely for internal staff. Hardware faults, software requests, access issues, and provisioning — all tracked with SLA enforcement.

**Who uses it:** All employees (submit tickets), IT team (resolve tickets)
**Filament Panel:** `it`
**Depends on:** [[HR — Employee Profiles]], Core
**Phase:** 6
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Employee ticket submission** — any tenant can submit an IT ticket from the `it` panel or a lightweight self-service form link; no IT panel login required for submission
- **Ticket categories** — hardware, software, access, network, other; category drives SLA policy selection
- **Priority levels** — critical/high/medium/low; critical tickets immediately page the on-call IT staff
- **SLA enforcement** — `sla_due_at` computed at ticket creation from the matching `sla_policies` record; `ITTicketSLABreached` event fires if ticket is not resolved before `sla_due_at`
- **Agent assignment** — IT team members assigned as `assigned_to`; reassignment tracked in message thread
- **Internal notes** — messages in `it_ticket_messages` can be marked `is_internal_note`; not visible to the submitting employee
- **Status workflow** — open → in_progress → pending_employee → resolved → closed; email notifications on each status change to submitting employee
- **Automatic provisioning ticket** — `EmployeeHired` event creates a standard access provisioning ticket listing all required system access based on the employee's department and role
- **Bulk ticket management** — bulk-assign, bulk-close, bulk-update priority from the Filament resource table
- **SLA policy management** — define response and resolution time targets per priority; mark one policy as default
- **Metrics dashboard** — first response time average, resolution time average, SLA breach rate, open tickets by category

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `it_tickets`
| Column | Type | Notes |
|---|---|---|
| `subject` | string | |
| `description` | text | |
| `category` | enum | `hardware`, `software`, `access`, `network`, `other` |
| `priority` | enum | `critical`, `high`, `medium`, `low` |
| `status` | enum | `open`, `in_progress`, `pending_employee`, `resolved`, `closed` |
| `tenant_id` | ulid FK | submitter → tenants |
| `assigned_to` | ulid FK nullable | → tenants (IT agent) |
| `sla_policy_id` | ulid FK nullable | → sla_policies |
| `sla_due_at` | timestamp nullable | |
| `first_response_at` | timestamp nullable | |
| `resolved_at` | timestamp nullable | |

### `it_ticket_messages`
| Column | Type | Notes |
|---|---|---|
| `it_ticket_id` | ulid FK | → it_tickets |
| `tenant_id` | ulid FK | author → tenants |
| `body` | text | |
| `is_internal_note` | boolean default false | hidden from submitter |
| `sent_at` | timestamp | |
| `attachments` | json nullable | array of file IDs |

### `sla_policies`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Critical SLA" |
| `response_time_hours` | integer | first response target |
| `resolution_time_hours` | integer | full resolution target |
| `priority` | enum | `critical`, `high`, `medium`, `low` |
| `is_default` | boolean default false | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ITTicketCreated` | `it_ticket_id`, `tenant_id` | Notification to assigned IT agent |
| `ITTicketSLABreached` | `it_ticket_id`, `priority` | Notification to IT manager |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeHired` | [[HR — Employee Profiles]] | Creates standard access provisioning `it_ticket` for the new employee |

---

## Permissions

```
it.it-tickets.view
it.it-tickets.create
it.it-tickets.edit
it.it-tickets.delete
it.it-tickets.assign
it.it-tickets.resolve
it.it-ticket-messages.view
it.it-ticket-messages.create
it.sla-policies.view
it.sla-policies.create
it.sla-policies.edit
it.sla-policies.delete
```

---

## Related

- [[IT Overview]]
- [[Customer Support & Helpdesk]]
- [[IT Asset Management]]
- [[Access & Permissions Audit]]
