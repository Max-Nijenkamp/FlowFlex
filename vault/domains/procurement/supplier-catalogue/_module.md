---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.catalogue
status: planned
build-status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [operations.suppliers, procurement.requisitions]
fires-events: []
consumes-events: []
patterns: [money]
tables: [proc_catalogue_items, proc_supplier_status]
permission-prefix: procurement.catalogue
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Supplier Catalogue

Curated catalogue of approved suppliers and their offered products/services with negotiated pricing. Drives requisition item selection. Also owns the supplier approval/blacklist status consulted everywhere in procurement.

Hosted in **/operations** (Procurement nav → Suppliers). See [[../_index|Procurement MOC]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../operations/suppliers/_module\|operations.suppliers]] | supplier link (`ops_supplier_id`); standalone supplier names otherwise |
| Soft | [[../requisitions/_module\|procurement.requisitions]] | catalogue picker consumer |

---

## Core Features

- [[features/catalogue-items\|Catalogue items]] — product/service, supplier, negotiated price, validity window, lead time.
- [[features/supplier-status\|Supplier status]] — approved / pending / blacklisted; the `SupplierGate` consulted everywhere.
- [[features/preferred-supplier\|Preferred supplier]] — one preferred supplier per category.
- [[features/supplier-portal\|Supplier self-onboarding portal]] — public Vue surface for suppliers to submit details/docs. *(assumed — not in v1 spec)*

---

## Data Model

Full model + ERD: [[data-model]]. Owns `proc_catalogue_items`, `proc_supplier_status`.

## DTOs

`CreateCatalogueItemData`, `SetSupplierStatusData` — [[api]].

## Services & Actions

`CatalogueService::search`, `SupplierGate::isBlocked`. See [[architecture]] + [[api]].

---

## Filament

**Nav group:** Suppliers (Procurement)

| Artifact | UI kind | Feature |
|---|---|---|
| `CatalogueItemResource` | simple-resource | [[features/catalogue-items]] |
| `SupplierStatusResource` | simple-resource | [[features/supplier-status]] |
| Supplier onboarding portal | public-vue | [[features/supplier-portal]] |

**Access contract:** `canAccess() = Auth::user()->can('procurement.catalogue.view-any') && BillingService::hasModule('procurement.catalogue')` — [[../../../architecture/filament-patterns]] #1. Portal uses a scoped guest/invite guard. See [[security]].

---

## Permissions

`procurement.catalogue.view-any` · `procurement.catalogue.manage` · `procurement.catalogue.manage-supplier-status`

---

## Cross-Domain Edges

- **Consumes (read):** `operations.suppliers` for the supplier link (soft; standalone names otherwise).
- **Feeds:** `SupplierGate::isBlocked` is called by [[../requisitions/_module|requisitions]], [[../purchase-orders/_module|PO sourcing]] to block blacklisted suppliers; `CatalogueService::search` feeds the requisition picker.
- **Data ownership:** writes **only** `proc_catalogue_items`, `proc_supplier_status`. It does **not** write `ops_suppliers` — it references them read-only ([[../../../security/data-ownership]]).

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Search excludes inactive, out-of-window, blacklisted-supplier items
- [ ] Blacklist blocks requisition picker + sourcing + PO supplier
- [ ] Blacklist requires notes
- [ ] One status row per supplier

## Build Manifest

```
database/migrations/xxxx_create_proc_catalogue_items_table.php
database/migrations/xxxx_create_proc_supplier_status_table.php
app/Models/Procurement/{CatalogueItem,SupplierStatus}.php
app/Data/Procurement/{CreateCatalogueItemData,SetSupplierStatusData}.php
app/Services/Procurement/CatalogueService.php
app/Support/Procurement/SupplierGate.php
app/Filament/Operations/Resources/{CatalogueItemResource,SupplierStatusResource}.php
database/factories/Procurement/CatalogueItemFactory.php
tests/Feature/Procurement/{CatalogueTest,SupplierBlacklistTest}.php
```

## Related

- [[../../operations/suppliers/_module]] · [[../requisitions/_module]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
