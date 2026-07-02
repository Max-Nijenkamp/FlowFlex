---
domain: it
module: helpdesk
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Helpdesk — Data Model

Tables owned: `it_tickets`, `it_ticket_replies`.

---

## it_tickets

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| ticket_number | string | unique per company | sequential per company |
| title | string | not null | |
| description | text | not null | |
| requester_employee_id | ulid | FK hr_employees | the reporting employee |
| category | string | in set | hardware / software / access / network / account |
| request_type | string | in set | incident / service-request |
| priority | string | in set | urgent / high / normal / low |
| status | string | default `open` | state machine (open/in_progress/resolved/closed) |
| assignee_id | ulid | nullable, FK users | IT staff member |
| asset_id | ulid | nullable | soft link to `it_assets` (read/FK only) |
| resolved_at | timestamp | nullable | stamped on resolve |
| closed_at | timestamp | nullable | stamped on close (manual or auto-close 3d) |
| deleted_at | timestamp | nullable | soft delete |

**Indexes:** `(company_id, status, priority)`, `(company_id, assignee_id, status)`

---

## it_ticket_replies

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| ticket_id | ulid | FK it_tickets, cascade | parent ticket |
| author_id | ulid | FK users | who wrote the reply |
| body | text | not null | reply content |
| is_internal | boolean | default false | internal IT note — invisible to requester, no notification |
| created_at | timestamp | | |

---

## ERD

```mermaid
erDiagram
    it_tickets {
        ulid id PK
        ulid company_id
        string ticket_number
        string title
        text description
        ulid requester_employee_id FK
        string category
        string request_type
        string priority
        string status
        ulid assignee_id FK
        ulid asset_id FK
        timestamp resolved_at
        timestamp closed_at
        timestamp deleted_at
    }
    it_ticket_replies {
        ulid id PK
        ulid company_id
        ulid ticket_id FK
        ulid author_id FK
        text body
        boolean is_internal
        timestamp created_at
    }
    hr_employees {
        ulid id PK
    }
    it_assets {
        ulid id PK
    }

    it_tickets ||--o{ it_ticket_replies : "has replies"
    hr_employees ||--o{ it_tickets : "requests"
    it_assets ||--o{ it_tickets : "linked (soft)"
```

---

## DTOs

### CreateItTicketData
- `title` — required
- `description` — required
- `category` — required, in set (hardware / software / access / network / account)
- `request_type` — required, in set (incident / service-request)
- `priority` — default `normal`, in set (urgent / high / normal / low)
- `asset_id?` — nullable; for requesters restricted to their own assigned asset *(assumed)*

### ItReplyData
- `ticket_id` — ulid in company
- `body` — required
- `is_internal` — boolean (only IT staff may set true *(assumed)*)
