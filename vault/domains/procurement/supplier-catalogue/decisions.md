---
domain: procurement
module: supplier-catalogue
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Supplier Catalogue — Local Decisions

- **`SupplierGate` is the single blacklist chokepoint.** Every procurement write path calls it; no path re-implements the check.
- **Supplier link is soft.** `supplier_id` points at an `ops_supplier` when Operations is active, else stores a local name — avoids a hard Operations dep for the catalogue.
- **Supplier self-onboarding portal added** (public Vue) beyond the v1 spec, driven by the "manual onboarding is the biggest P2P pain / $35k→$2.4k automated" finding ([[../_opportunities]]). Portal writes only pending/draft rows this module owns.

> [!warning] UNVERIFIED
> The supplier portal is **not in the original v1 module spec** — added per the full-map mandate ("supplier portal public-vue"). Scope/priority needs product confirmation; may defer to Phase 2 alongside `laravel/socialite`.

## Related

- [[_module]] · [[unknowns]] · [[../_opportunities]]
