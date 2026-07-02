---
domain: support
module: support-analytics
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — Data Model

## sup_csat_responses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| ticket_id FK | ulid | unique — one response per ticket |
| rating | int | 1–5 |
| comment | text nullable | |
| token | uuid | unique — public response link |
| responded_at | timestamp nullable | null = sent, unanswered |

The **only** table this module owns. All other metrics aggregate read-only from `sup_tickets`, `sup_ticket_replies`, `sup_sla_events`.

---

## ERD

```mermaid
erDiagram
    sup_csat_responses {
        ulid id PK
        ulid company_id FK
        ulid ticket_id FK
        int rating
        text comment
        uuid token
        timestamp responded_at
    }
    sup_tickets ||--o| sup_csat_responses : "one CSAT per resolved ticket"
```

> Cross-domain reads (no writes): `sup_tickets` + `sup_ticket_replies` (owned by [[../tickets/_module|support.tickets]]) and `sup_sla_events` (owned by [[../sla/_module|support.sla]]) power the aggregate metrics.
