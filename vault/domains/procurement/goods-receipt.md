---
type: module
domain: Procurement
panel: operations
module-key: procurement.goods-receipt
status: planned
color: "#4ADE80"
---

# Goods Receipt Notes

Confirm receipt of ordered goods/services against POs and enable 3-way matching (PO ↔ GRN ↔ invoice) for payment approval.

> Shares the GRN concept with [[domains/operations/goods-receipt]]. When Operations is active, that module owns GRN; Procurement adds the 3-way match approval gate for Finance. When Operations is inactive, Procurement provides standalone GRN.

## Core Features

- Record receipt against a PO (full or partial)
- Service confirmation (for non-physical purchases)
- 3-way match: compare PO, GRN, and supplier invoice — flag mismatches
- Match approval gate: invoice cannot be paid until matched
- Discrepancy resolution workflow
- Receipt history

## Data Model

Uses `ops_goods_receipts` + `ops_grn_lines` when Operations active. Adds:

| Table | Key Columns |
|---|---|
| `proc_three_way_matches` | company_id, po_id, grn_id, invoice_id, match_status (matched/discrepancy), approved_for_payment, notes |

## Filament

**Nav group:** Purchase Orders

- `ThreeWayMatchResource` — match queue, approve for payment, resolve discrepancies

## Cross-Domain / Events

- Match approval → Finance AP (release for payment)

## Related

- [[domains/operations/goods-receipt]]
- [[domains/finance/accounts-payable]]
