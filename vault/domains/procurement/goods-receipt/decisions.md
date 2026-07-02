---
domain: procurement
module: goods-receipt
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — Local Decisions

- **Read-only reconciliation, own verdict table.** Reconciles three domains' documents, writes only `proc_three_way_matches`. The AP payment gate is a read-hook raising `MatchFailedException`, not an AP-table write.
- **Auto-approve within tolerance** (±2% or €10, configurable) so clean matches need no human touch. *(assumed defaults)*
- **Override is the only way past a discrepancy**, permission-gated + notes-required + audited.
- **Layer over Operations GRN** — single GRN model, no standalone GRN fallback. *(assumed)*

> [!warning] UNVERIFIED
> Tolerance defaults (±2% / €10) and whether tolerances are per-company or per-category are assumed — no source in the vault. Segregation-of-duties on override (overrider ≠ bill creator) is assumed, not specified.

## Related

- [[_module]] · [[unknowns]] · [[../../../security/data-ownership]]
