---
domain: procurement
module: goods-receipt
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — Open Questions

- Tolerance defaults (±2% / €10) + per-company vs per-category configurability. **UNVERIFIED**.
- Partial-receipt matching: match per receipt vs cumulative against PO. `*(assumed: cumulative accepted qty)*`
- 2-way match (PO↔bill, no GRN) for service purchases — supported path? `*(assumed: service GRN with service flag)*`
- Segregation of duties on override (overrider ≠ bill creator/approver). `*(assumed)*` — confirm at build.
- Auto-blacklist / supplier scorecard feed on repeated mismatches? `*(assumed: not v1)*`

## Related

- [[_module]] · [[decisions]]
