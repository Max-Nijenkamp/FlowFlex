---
type: module
domain: Procurement
panel: operations
module-key: procurement.catalogue
status: planned
color: "#4ADE80"
---

# Supplier Catalogue

Curated catalogue of approved suppliers and their offered products/services with negotiated pricing. Drives requisition item selection.

## Core Features

- Approved supplier list (links to Operations suppliers if active)
- Catalogue items: product/service, supplier, negotiated price, lead time
- Preferred supplier per category
- Supplier approval status (approved/pending/blacklisted)
- Price agreements / contracts per supplier
- Catalogue search for requisition creation
- Punch-out style selection: pick items into a requisition

## Data Model

| Table | Key Columns |
|---|---|
| `proc_catalogue_items` | company_id, supplier_id, name, category, description, agreed_price_cents, lead_time_days, is_active |
| `proc_supplier_status` | company_id, supplier_id, status, approved_at, notes |

## Filament

**Nav group:** Suppliers

- `CatalogueItemResource` — manage catalogue
- Supplier approval status management
- Catalogue picker used in requisition creation

## Related

- [[domains/operations/suppliers]]
- [[domains/procurement/requisitions]]
