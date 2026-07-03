---
domain: operations
module: suppliers
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers

Supplier/vendor records with contacts, payment terms, and a supplied-items catalogue. **Operational** supplier data only — financial supplier records (IBAN, bills) live in [[../../finance/accounts-payable/_module|finance.ap]] and link via `fin_supplier_id`.

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.suppliers`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.suppliers`
**Tables:** `ops_suppliers`, `ops_supplier_items`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../inventory/_module\|operations.inventory]] | supplied-items catalogue references items |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../../finance/accounts-payable/_module\|finance.ap]] | `fin_supplier_id` billing link; standalone otherwise |

---

## Core Features

- Supplier record: name, contact, email, phone (E.164), address, payment terms, currency
- Supplied-items catalogue: which items a supplier provides, at what cost, lead time; one preferred supplier per item
- Supplier performance: on-time delivery (GRN received_at vs PO expected), order history
- Contact log (simple notes *(assumed)*)

See features: [[./features/supplier-catalogue|Supplied-Items Catalogue]] · [[./features/supplier-performance|Supplier Performance]].

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's suppliers data
- [ ] Module gating: artifacts hidden when `operations.suppliers` inactive
- [ ] One preferred supplier per item enforced
- [ ] On-time rate from PO/GRN fixtures
- [ ] Phone normalised to E.164
- [ ] finance.ap link optional — no error when module inactive

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | PO + GRN data (same domain) | operations.purchase-orders, operations.goods-receipt | performance metrics |
| Links | `fin_supplier_id` | finance.ap | optional billing reference (read-only) |

**Data ownership:** `operations.suppliers` writes only `ops_suppliers`, `ops_supplier_items`. It **reads** PO/GRN rows for performance metrics but never writes them; it stores a `fin_supplier_id` reference to finance.ap's supplier but never writes finance tables ([[../../../security/data-ownership]]).

---

## Related

- [[../purchase-orders/_module|operations.purchase-orders]]
- [[../inventory/_module|operations.inventory]]
- [[../../finance/accounts-payable/_module|finance.ap]]
- [[../_index|Operations MOC]]
