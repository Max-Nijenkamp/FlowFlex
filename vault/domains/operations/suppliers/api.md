---
domain: operations
module: suppliers
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers — DTOs & API

## DTOs

### CreateSupplierData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required, max:255 |
| contact_name | ?string | nullable, max:255 |
| email | ?string | nullable, email |
| phone | ?string | nullable, phone:AUTO → E.164 |
| payment_terms_days | int | required, min:0 |
| currency | string | required, size:3, valid ISO 4217 |
| address | ?array | nullable |
| fin_supplier_id | ?string | nullable, ulid |

### LinkSupplierItemData (input)

| Field | Type | Validation |
|---|---|---|
| supplier_id | string | required, ulid, exists in company |
| item_id | string | required, ulid, exists in company |
| supplier_sku | ?string | nullable, max:64 |
| cost_cents | int | required, min:0 |
| lead_time_days | ?int | nullable, min:0 |
| is_preferred | bool | default false — true unsets other preferred for the item |

---

## Output

### SupplierData (output)

`id`, `name`, `contact_name`, `email`, `phone`, `payment_terms_days`, `currency`, `is_active`, `fin_supplier_id`, `on_time_rate` (computed), `order_count` (computed), `supplied_item_count`.

---

## Public / Portal Endpoints

None. Authenticated `operations` panel only.
