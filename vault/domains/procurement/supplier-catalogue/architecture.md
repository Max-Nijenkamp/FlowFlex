---
domain: procurement
module: supplier-catalogue
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Supplier Catalogue — Architecture

## Shape

Two service surfaces over the module's own tables:

- `CatalogueService` — search/read of active, in-window, approved-supplier items.
- `SupplierGate` — a small support class (`isBlocked(supplierId)`) that every procurement write path consults before letting a blacklisted supplier through.

```mermaid
flowchart LR
    REQ`requisitions picker` -->|search| CS[CatalogueService]
    CS -->|reads| CI[(proc_catalogue_items)]
    CS -->|filters by| SS[(proc_supplier_status)]
    REQ & PO`PO sourcing` -->|isBlocked| SG[SupplierGate]
    SG --> SS
    OPS`operations.suppliers read` -.link.-> CI
    PORTAL[Supplier portal Vue] -->|submits| PEND[(pending status + draft items)]
```

## Key decisions

- **`SupplierGate` is the single blacklist chokepoint** — requisitions, sourcing, and PO supplier selection all call it; no path duplicates the check.
- **Search excludes non-eligible items server-side** (active + in-window + approved supplier) so the picker can't surface a blocked item.
- **Supplier link is soft** — `supplier_id` is an `ops_supplier` id when Operations is active, else a local name string.
- **Money** as integer cents (brick/money) for `agreed_price_cents`.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/patterns/actions-pattern]]
