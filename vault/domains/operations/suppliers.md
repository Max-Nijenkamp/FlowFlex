---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.suppliers
status: planned
priority: p3
depends-on: [operations.inventory, core.billing, core.rbac]
soft-depends: [finance.ap]
fires-events: []
consumes-events: []
patterns: []
tables: [ops_suppliers, ops_supplier_items]
permission-prefix: operations.suppliers
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Suppliers

Supplier/vendor records with contact details, payment terms, and supplied items catalogue. Operational supplier data — financial supplier records (IBAN, bills) live in [[domains/finance/accounts-payable|finance.ap]] and link via `fin_supplier_id`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/inventory\|operations.inventory]] | supplied-items catalogue references items |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | `fin_supplier_id` link for billing; standalone otherwise |

---

## Core Features

- Supplier record: name, contact person, email, phone, address, payment terms, currency
- Supplied items: which inventory items this supplier provides, at what cost, lead time
- Supplier performance: on-time delivery rate (GRN received_at vs PO expected), order history
- Preferred supplier flagging per item (one preferred per item)
- Supplier contact log (notes *(assumed: simple notes field/relation)*)
- Phone validation via `propaganistas/laravel-phone`

---

## Data Model

### ops_suppliers

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| contact_name / email / phone | string nullable | phone E.164 |
| address | jsonb nullable | |
| payment_terms_days | int default 30 | |
| currency | string(3) | |
| fin_supplier_id | ulid nullable | finance.ap link |
| is_active | boolean | |
| deleted_at | timestamp nullable | |

### ops_supplier_items — id, supplier_id FK, item_id FK, company_id; unique `(supplier_id, item_id)`; supplier_sku, cost_cents, lead_time_days, is_preferred (one per item)

---

## DTOs

### CreateSupplierData — name (required), email (email), phone (phone:AUTO), payment_terms_days (min:0), currency
### LinkSupplierItemData — supplier_id, item_id, cost_cents (min:0), lead_time_days, is_preferred (unsets other preferred)

## Services & Actions

- `SupplierService::performance(string $supplierId): array{on_time_rate: float, order_count: int}` — from POs + GRNs
- `PreferredSupplierFor::item(string $itemId): ?Supplier`

---

## Filament

**Nav group:** Purchasing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OpsSupplierResource` | #1 CRUD resource | supplied-items relation manager, performance + order history on view |

---

## Permissions

`operations.suppliers.view-any` · `operations.suppliers.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] One preferred supplier per item enforced
- [ ] On-time rate from PO/GRN fixtures
- [ ] Phone normalised E.164
- [ ] finance.ap link optional, no error when module inactive

---

## Build Manifest

```
database/migrations/xxxx_create_ops_suppliers_table.php
database/migrations/xxxx_create_ops_supplier_items_table.php
app/Models/Operations/{OpsSupplier,SupplierItem}.php
app/Data/Operations/{CreateSupplierData,LinkSupplierItemData}.php
app/Services/Operations/SupplierService.php
app/Filament/Operations/Resources/OpsSupplierResource.php
database/factories/Operations/{OpsSupplierFactory,SupplierItemFactory}.php
tests/Feature/Operations/SupplierTest.php
```

---

## Related

- [[domains/operations/purchase-orders]]
- [[domains/operations/inventory]]
- [[domains/finance/accounts-payable]]
