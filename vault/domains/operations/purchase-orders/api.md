---
domain: operations
module: purchase-orders
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Purchase Orders — DTOs & API

## DTOs

### CreatePoData (input)

| Field | Type | Validation |
|---|---|---|
| supplier_id | string | required, ulid, exists in company, active |
| expected_delivery | ?CarbonImmutable | nullable, date, after_or_equal:today *(assumed)* |
| currency | string | required, size:3 — defaults from supplier |
| lines | array | required, min:1 |
| lines[].item_id | string | required, ulid, exists in company |
| lines[].quantity_ordered | decimal | required, gt:0 |
| lines[].unit_cost_cents | ?int | nullable, min:0 — defaults from supplier catalogue; error "No cost known for this item from this supplier." when neither given |

---

### PoData (output)

`id`, `po_number`, `supplier_id`, `supplier_name`, `status`, `expected_delivery`, `total_cents`, `total_formatted`, `currency`, `requisition_id`, `pdf_path`, `lines[]` (item, ordered, received, unit cost, line total), `receipt_progress` (received/ordered %).

---

## Public / Portal Endpoints

None. The PO PDF is emailed to the supplier (outbound) but there is no public supplier portal endpoint in v1. Authenticated `operations` panel only.
