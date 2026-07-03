---
domain: procurement
module: supplier-catalogue
feature: catalogue-items
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Catalogue Items

Managed list of products/services with a supplier, negotiated price, validity window, and lead time. The source for requisition item selection.

## Behaviour

- Item: supplier, name, description, category, agreed_price_cents, currency, valid_from/until, lead_time_days, is_active.
- Creating an item for a blacklisted supplier is rejected.
- `CatalogueService::search` returns only active, in-window, approved-supplier items.

## UI

- **Kind**: simple-resource
- **Page**: "Catalogue Items" (`/operations/procurement/catalogue`)
- **Layout**: table — name, supplier, category, price, validity badge (in/out of window), lead time, active toggle.
- **Key interactions**: create/edit form (supplier picker, category, price, date range, lead time); category filter; validity badges.
- **States**: empty ("Add your first catalogue item" CTA) · loading (skeleton) · error (toast + field errors) · saved (row flash); expired items show an amber "out of window" badge.
- **Gating**: view `procurement.catalogue.view-any`; edit `procurement.catalogue.manage`.

## Data

- Owns / writes: `proc_catalogue_items`.
- Reads: `operations.suppliers` (soft) for the supplier picker; `proc_supplier_status` for the blacklist check.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `CatalogueService::search` → requisition [[../../requisitions/features/catalogue-picker|catalogue picker]]; agreed price → spend [[../../spend-analytics/features/savings-tracking|savings]].
- Shared entity: suppliers (operations, read-only).

## Test Checklist

### Unit
- [ ] Eligibility: active + within validity window + approved supplier; `agreed_price_cents` integer

### Feature (Pest)
- [ ] Search excludes non-eligible items server-side (expired window, blocked supplier)
- [ ] Soft supplier link: ops id when Operations active, local name string otherwise
- [ ] Tenant isolation + permission on item CRUD

### Livewire
- [ ] `CatalogueItemResource` validates window/price; guarded delete; hidden without permission/module

## Unknowns

- Price-agreement expiry notifications? `*(assumed: not v1)*`

## Related

- [[../_module|Supplier Catalogue]] · [[supplier-status]] · [[preferred-supplier]]
