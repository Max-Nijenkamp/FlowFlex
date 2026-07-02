---
domain: procurement
module: purchase-orders
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement PO Layer — Data Model

Owns `proc_po_sourcing`. Adds one column (`procurement_approved_at`) to the Operations-owned `ops_purchase_orders` — a documented schema extension ([[decisions]]).

## ERD

```mermaid
erDiagram
    ops_purchase_orders ||--o{ proc_po_sourcing : "collects quotes"
    ops_purchase_orders ||--o{ ops_po_lines : has
    proc_po_sourcing {
        ulid id PK
        ulid po_id FK "ops_purchase_orders"
        ulid company_id FK
        ulid supplier_id "ops_suppliers"
        bigint quote_amount_cents
        string quote_reference "nullable"
        bool selected "max one per PO"
    }
    ops_purchase_orders {
        ulid id PK "owned by operations"
        timestamp procurement_approved_at "column added by this module"
    }
    ops_po_lines {
        ulid id PK "owned by operations"
    }
```

## proc_po_sourcing

| Column | Type | Notes |
|---|---|---|
| id, po_id FK, company_id (indexed) | ulid | |
| supplier_id | ulid FK ops_suppliers | not blacklisted (SupplierGate) |
| quote_amount_cents | bigint | brick/money |
| quote_reference | string nullable | |
| selected | boolean default false | **max one selected per PO** |

## Shared / read tables

- `ops_purchase_orders`, `ops_po_lines` — **owned by Operations**; read here for display + commitment math. Business writes go through Operations' service.
- `procurement_approved_at` on `ops_purchase_orders` — added + written by this module (approval/send-gate), read by the send path.

## Integrity rules

- At most one `selected = true` per `po_id`.
- Quote supplier must not be blacklisted at add + select time.

## Related

- [[_module]] · [[architecture]] · [[api]] · [[../../../security/data-ownership]] · [[decisions]]
