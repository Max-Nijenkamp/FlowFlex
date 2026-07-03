---
domain: procurement
module: requisitions
feature: catalogue-picker
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Catalogue Picker

Punch-out-style selection: pick approved catalogue items straight into a requisition, pre-filling name, supplier, agreed price.

## Behaviour

- Search the supplier catalogue (`CatalogueService::search`) — active, in-window, approved-supplier items only.
- Selecting an item adds a requisition line with `catalogue_item_id`, name, agreed unit price (editable qty).
- Blacklisted-supplier items are excluded from the picker ([[../../supplier-catalogue/features/supplier-status]]).

## UI

- **Kind**: widget (a picker component embedded in the requisition form; not a standalone page).
- **Page**: none — modal/slide-over within `RequisitionResource`.
- **Layout**: search box + category filter → result list (name, supplier, price, lead time); "add" per row.
- **Key interactions**: type-ahead search; add item → new line appears with agreed price; free-text line still allowed alongside.
- **States**: empty ("No catalogue items — add a free-text line") · loading (result skeletons) · error (toast) · selected (added items badged in the line list).
- **Gating**: within `procurement.requisitions.create`; results limited to items the catalogue exposes.

## Data

- Owns / writes: `proc_requisition_items` (the picked line) — its own table.
- Reads: `procurement.catalogue` `CatalogueService::search` (read-only, same domain but separate module — via service, not direct table write).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: catalogue items + supplier status from [[../../supplier-catalogue/_module|procurement.catalogue]].
- Feeds: nothing.

## Test Checklist

### Unit
- [ ] Picker pre-fills name, supplier, agreed price from the catalogue item

### Feature (Pest)
- [ ] Picker lists only eligible items (server-side filter: active + in-window + approved supplier); blacklisted supplier's items absent
- [ ] `procurement.catalogue` inactive -> free-text items only, no error
- [ ] Tenant isolation on the picker query

### Livewire
- [ ] Items repeater integrates the picker; manual override of price allowed *(assumed)* and flagged for savings tracking

## Unknowns

- True cXML punch-out to external supplier sites (vs internal catalogue only) — differentiator, deferred ([[../../_opportunities]]). `*(assumed: internal catalogue v1)*`

## Related

- [[../_module|Requisitions]] · [[../../supplier-catalogue/features/catalogue-items]] · [[create-requisition]]
