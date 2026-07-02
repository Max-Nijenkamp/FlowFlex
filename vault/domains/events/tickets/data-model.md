---
domain: events
module: tickets
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets — Data Model

## `ev_tickets`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `event_id` | ulid | FK → `ev_events` |
| `name` | string | |
| `price_cents` | bigint | 0 = free |
| `currency` | string(3) | |
| `quantity_available` | int nullable | null = unlimited |
| `quantity_sold` | int | default 0; atomic |
| `sales_start` / `sales_end` | timestamp nullable | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `ev_ticket_purchases`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `ticket_id` | ulid | FK → `ev_tickets` |
| `registration_id` | ulid | FK → `ev_registrations`, unique |
| `amount_cents` | bigint | Post-discount |
| `currency` | string(3) | |
| `stripe_payment_intent_id` | string nullable | Unique |
| `status` | string | pending / paid / refunded |
| `discount_code` | string nullable | |

## `ev_ticket_discounts` *(formalised)*

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `event_id` | ulid | FK → `ev_events` |
| `code` | string | Unique per event |
| `type` | string | percent / fixed |
| `value` | int | Percent (0–100) or fixed cents |
| `max_uses` | int nullable | |
| `used_count` | int | default 0 |

## ERD

```mermaid
erDiagram
    ev_events ||--o{ ev_tickets : "offers"
    ev_events ||--o{ ev_ticket_discounts : "codes"
    ev_tickets ||--o{ ev_ticket_purchases : "sold as"
    ev_registrations ||--|| ev_ticket_purchases : "confirms"

    ev_tickets {
        ulid id PK
        ulid event_id FK
        bigint price_cents
        string currency
        int quantity_available
        int quantity_sold
        timestamp sales_start
        timestamp sales_end
    }
    ev_ticket_purchases {
        ulid id PK
        ulid ticket_id FK
        ulid registration_id FK
        bigint amount_cents
        string stripe_payment_intent_id
        string status
        string discount_code
    }
    ev_ticket_discounts {
        ulid id PK
        ulid event_id FK
        string code
        string type
        int value
        int max_uses
        int used_count
    }
    ev_registrations { ulid id PK }
    ev_events { ulid id PK }
```
