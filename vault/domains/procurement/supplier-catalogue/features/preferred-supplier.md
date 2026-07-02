---
domain: procurement
module: supplier-catalogue
feature: preferred-supplier
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Preferred Supplier per Category

Mark one preferred supplier per category so requisitioners are nudged toward negotiated agreements (guided buying), reducing maverick spend.

## Behaviour

- At most one preferred supplier per `(company, category)`.
- The requisition catalogue picker surfaces preferred-supplier items first.
- Non-preferred purchases in a category are flagged as candidate maverick spend downstream.

## UI

- **Kind**: simple-resource (a toggle/action within the catalogue/status resource, not a page).
- **Page**: none — "Set preferred" action on catalogue items / supplier-status rows, grouped by category.
- **Layout**: preferred badge on the item table; setting one clears the previous preferred in that category.
- **Key interactions**: toggle preferred → confirm swap if another exists.
- **States**: empty (no preferred set for a category) · loading · error (toast) · selected (preferred badge).
- **Gating**: `procurement.catalogue.manage`.

## Data

- Owns / writes: preferred flag on `proc_catalogue_items` (or a small own pivot) — this module's own tables ([[../../../../security/data-ownership]]).
- Reads: nothing extra.
- Cross-domain writes: none.

## Relations

- Feeds: preferred ranking → requisition picker; "off-preferred" → spend maverick detection.

## Unknowns

- Storage: `is_preferred` flag vs category→supplier pivot. `*(assumed: flag)*` — see [[../data-model]].

## Related

- [[../_module|Supplier Catalogue]] · [[catalogue-items]] · [[../../spend-analytics/features/maverick-spend]]
