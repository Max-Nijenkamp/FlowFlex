---
domain: procurement
module: spend-analytics
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — Open Questions

- Per-department / per-cost-centre view scoping vs a single `view` permission. `*(assumed: single view v1)*`
- "Actual" spend definition: received-value vs invoiced-value vs paid. `*(assumed: PO/received value)*` — reconcile with 3-way-match + finance.
- Maverick rule precision: off-catalogue only, or also non-preferred-supplier within catalogue. `*(assumed: off-catalogue OR non-approved supplier)*`
- Savings baseline when no agreed price exists (net-new items). `*(assumed: excluded from savings)*`
- Whether spend should also read Finance AP actuals (paid bills) for a truer "actual". **UNVERIFIED**.

## Related

- [[_module]] · [[decisions]]
