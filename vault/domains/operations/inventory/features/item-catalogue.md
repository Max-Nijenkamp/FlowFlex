---
domain: operations
module: inventory
feature: item-catalogue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Item Catalogue

CRUD of inventory items (SKU, name, unit, cost, reorder point) with per-warehouse level display.

## Behaviour

- Create/edit/delete items; `sku` unique per company.
- View shows per-warehouse levels (on-hand / reserved / available) as a read-only relation — these come from `ops_stock_levels` and are never edited on the form.
- Import items in bulk via Core Data Import (soft dep).
- Soft-delete blocked while stock > 0 *(assumed)*.

## UI

- **Kind**: simple-resource.
- **Page**: `ItemResource` at `/operations/items`.
- **Layout**: table (SKU, name, category, unit, cost, total available, low-stock badge); form (SKU, name, category, unit, cost in euros→cents, reorder point); view page adds a per-warehouse levels panel.
- **Key interactions**: create/edit item; SKU search + category filter; low-stock filter toggle; row link to movement history.
- **States**: empty (no items → "add your first item / import" CTA) · loading (table skeleton) · error (duplicate SKU inline validation) · selected (row → view with levels).
- **Gating**: view `operations.inventory.view-any`; create/edit `operations.inventory.manage-items`.

## Data

- Owns / writes: `ops_items`.
- Reads: `ops_stock_levels` (own module) for the levels panel; warehouse names from operations.warehouses.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: nothing directly; items are referenced by PO lines, GRN lines, supplier catalogue, adjustments.
- Shared entity: `ops_warehouses` (operations.warehouses).

## Related

- [[../_module|Inventory]] · [[./stock-movements|Stock Movements]] · [[../../../core/data-import/_module|core.import]]
