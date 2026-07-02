---
domain: finance
module: fixed-assets
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Units-of-production deferred** — v1 ships straight-line + declining only; units-of-production is deferred. *(assumed)*

## UNVERIFIED gaps

- **Declining-balance rate basis** — the spec names "declining balance" but does not specify the rate (e.g. double-declining = 2 × straight-line rate, or a stored per-asset/per-category rate). The data model carries no rate column, so the basis is unresolved.
- **Category default settings** — categories are said to carry "default depreciation settings" applied at create, but the spec does not define where category defaults are stored (no category table in the data model) or which fields they default (method? useful_life? salvage?).
- **GL account mapping** — which ledger accounts depreciation and disposal entries post to (depreciation expense / accumulated depreciation / disposal gain-loss) is not enumerated in the spec.
- **`AssetData` output shape** — referenced as a service return type but its fields are not fully enumerated; NBV inclusion is inferred from the FixedAssetResource "NBV column" note.

No build-blocking unknowns identified.
