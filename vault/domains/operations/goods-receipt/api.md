---
domain: operations
module: goods-receipt
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Goods Receipt — DTOs & API

## DTOs

### CreateGrnData (input)

| Field | Type | Validation |
|---|---|---|
| po_id | string | required, ulid, PO in `sent`/`partially_received` |
| warehouse_id | string | required, ulid, exists in company |
| lines | array | required, min:1 |
| lines[].po_line_id | string | required, ulid, belongs to the PO |
| lines[].quantity_received | decimal | required, gt:0 |
| lines[].quantity_accepted | decimal | required, min:0 |
| lines[].quantity_rejected | decimal | required, min:0 |
| lines[].reject_reason | ?string | required_if rejected > 0 |

**Line rule:** `quantity_accepted + quantity_rejected = quantity_received`.
**Cumulative rule:** received-to-date ≤ `quantity_ordered × 1.1` *(assumed 10% tolerance)*.

---

### GrnData (output)

`id`, `grn_number`, `po_id`, `po_number`, `warehouse_id`, `received_at`, `received_by_name`, `status`, `accepted_total_cents`, `currency`, `lines[]` (item, received, accepted, rejected, reject_reason).

---

## Public / Portal Endpoints

None. Authenticated `operations` panel only.
