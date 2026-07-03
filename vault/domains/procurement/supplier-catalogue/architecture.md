---
domain: procurement
module: supplier-catalogue
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CatalogueItemResource` | #1 CRUD resource | badge-status, validity-window filter, guarded-delete | Negotiated items; server-side eligibility filtering feeds the requisition picker |
| `SupplierStatusResource` | #1 CRUD resource | badge-status (approved / pending / blacklisted), audited status actions | Status flips consulted by `SupplierGate` |

Admin surfaces hosted in **/operations** (Settings nav group); the supplier portal is a public Vue + Inertia surface (ui-strategy rows 12-16) on a scoped invite/guest guard, NOT a Filament artifact. Admin artifacts gate on `canAccess() = Auth::user()->can('procurement.catalogue.view-any') && BillingService::hasModule('procurement.catalogue')` per [[../../../architecture/filament-patterns]] #1.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Catalogue item CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Supplier status flip (approve / blacklist) | Pessimistic | Status row locked -- `SupplierGate` decisions must not read a half-flipped state; audited action fires once |
| Portal submissions | n-a | Insert-only pending rows, reviewed before activation |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/patterns/actions-pattern]]
