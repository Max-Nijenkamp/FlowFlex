---
domain: procurement
module: goods-receipt
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — Data Model

Owns `proc_three_way_matches`. Reads (never writes) `ops_purchase_orders`, `ops_grn*`, `finance_ap_bills`.

## ERD

```mermaid
erDiagram
    ops_purchase_orders ||--o{ proc_three_way_matches : "po_id"
    ops_grn ||--o{ proc_three_way_matches : "grn_id"
    finance_ap_bills ||--o{ proc_three_way_matches : "bill_id"
    proc_three_way_matches {
        ulid id PK
        ulid company_id FK
        ulid po_id "read from operations"
        ulid grn_id "read from operations"
        ulid bill_id "read from finance.ap"
        string match_status "matched|quantity-discrepancy|amount-discrepancy|overridden"
        bool approved_for_payment "default false"
        bigint variance_cents "bill - (GRN accepted x PO price)"
        text notes "required on override"
        timestamp matched_at
    }
```

## proc_three_way_matches

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| po_id / grn_id / bill_id | ulid | **unique triple**; read-only refs to other domains |
| match_status | string | matched / quantity-discrepancy / amount-discrepancy / overridden |
| approved_for_payment | boolean default false | the gate flag |
| variance_cents | bigint | brick/money |
| notes | text nullable | required on override |
| matched_at | timestamp | |

## Integrity rules

- Unique `(company_id, po_id, grn_id, bill_id)`.
- `approved_for_payment` can only become true via auto-match (within tolerance) or an audited override.

## Related

- [[_module]] · [[architecture]] · [[api]] · [[../../../security/data-ownership]]
