---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.catalogue
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [operations.suppliers, procurement.requisitions]
fires-events: []
consumes-events: []
patterns: [money]
tables: [proc_catalogue_items, proc_supplier_status]
permission-prefix: procurement.catalogue
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Supplier Catalogue

Curated catalogue of approved suppliers and their offered products/services with negotiated pricing. Drives requisition item selection.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/operations/suppliers\|operations.suppliers]] | supplier link (`ops_supplier_id`); standalone supplier names otherwise |
| Soft | [[domains/procurement/requisitions\|procurement.requisitions]] | catalogue picker consumer |

---

## Core Features

- Approved supplier list (links Operations suppliers if active)
- Catalogue items: product/service, supplier, negotiated price, lead time
- Preferred supplier per category (one)
- Supplier approval status: approved / pending / blacklisted — **blacklisted suppliers blocked from requisitions, sourcing, and POs**
- Price agreements: validity windows per item *(assumed: valid_from/valid_until)*
- Catalogue search for requisition creation
- Punch-out style selection: pick items into a requisition

---

## Data Model

### proc_catalogue_items

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| supplier_id | ulid | ops supplier or local ref |
| name / description | string / text | |
| category | string | |
| agreed_price_cents | bigint | |
| currency | string(3) | |
| valid_from / valid_until | date nullable | agreement window |
| lead_time_days | int nullable | |
| is_active | boolean | |
| deleted_at | timestamp nullable | |

### proc_supplier_status — id, company_id (indexed), supplier_id (unique per company), status (approved/pending/blacklisted), approved_at nullable, notes

---

## DTOs

### CreateCatalogueItemData — supplier_id (not blacklisted — "This supplier is blacklisted."), name, category, agreed_price_cents (min:0), valid window (until ≥ from), lead_time_days
### SetSupplierStatusData — supplier_id, status (in set), notes (required for blacklisted)

## Services & Actions

- `CatalogueService::search(string $term, ?string $category): Collection` — active + in-window + approved-supplier items only
- `SupplierGate::isBlocked(string $supplierId): bool` — checked by requisitions/sourcing/PO paths

---

## Filament

**Nav group:** Suppliers (Procurement)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CatalogueItemResource` | #1 CRUD resource | category filter, validity badges |
| `SupplierStatusResource` | #1 CRUD resource | approve/blacklist with notes |

Catalogue picker component used in requisition form.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('procurement.catalogue.view-any') && BillingService::hasModule('procurement.catalogue')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`procurement.catalogue.view-any` · `procurement.catalogue.manage` · `procurement.catalogue.manage-supplier-status`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Search excludes inactive, out-of-window, blacklisted-supplier items
- [ ] Blacklist blocks requisition picker + sourcing + PO supplier
- [ ] Blacklist requires notes
- [ ] One status row per supplier

---

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

---

## Related

- [[domains/operations/suppliers]]
- [[domains/procurement/requisitions]]
