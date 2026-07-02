---
domain: operations
module: suppliers
feature: supplier-catalogue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Supplied-Items Catalogue

Which items a supplier provides, at what cost, lead time, and vendor SKU — with a preferred supplier per item.

## Behaviour

- Link items to a supplier: `supplier_sku`, `cost_cents`, `lead_time_days`, `is_preferred`.
- Exactly one preferred supplier per item; setting a new one unsets the old.
- The preferred supplier's `cost_cents` is the default unit cost when adding that item to a PO line.
- Cost stored in minor units (brick/money).

## UI

- **Kind**: simple-resource — a relation manager on `OpsSupplierResource`, not a separate page.
- **Page**: supplied-items relation manager under `OpsSupplierResource` at `/operations/suppliers/{id}`.
- **Layout**: table of linked items (item, supplier SKU, cost, lead time, preferred badge); inline add/edit; preferred toggle.
- **Key interactions**: add item link; toggle preferred (unsets previous, confirm); edit cost/lead time.
- **States**: empty (no items linked → "link the items this supplier provides") · loading (skeleton) · error (duplicate supplier+item link rejected) · selected (row edit inline).
- **Gating**: view `operations.suppliers.view-any`; manage `operations.suppliers.manage`.

## Data

- Owns / writes: `ops_supplier_items`.
- Reads: `ops_items` (operations.inventory) for the item picker.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: preferred cost consumed by [[../../purchase-orders/_module|operations.purchase-orders]] as the PO line cost default (read via `PreferredSupplierFor::item`).
- Shared entity: `ops_items` (operations.inventory).

## Related

- [[../_module|Suppliers]] · [[../../purchase-orders/_module|Purchase Orders]]
