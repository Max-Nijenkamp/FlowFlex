---
domain: crm
module: revenue-intelligence
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Revenue Intelligence — Unknowns & Open Questions

## Assumptions

- Factor weights default to 30/30/20/20 (activity recency / stage velocity / engagement / deal age) *(assumed)*.
- `atRisk` threshold defaults to 40 *(assumed)*.
- A deal is considered "stalled" after 14 days with no activity *(assumed)*.
- ML-based scoring and LLM win/loss summaries are deferred to P3 *(assumed)*.

## Open Questions

- What are the exact factor formulas (e.g. how is "stage velocity vs average" normalised into a 0–100 contribution)?
- Should the at-risk threshold and stalled-days window be per-company configurable?
- Which cycle-norm baseline drives "deal age vs cycle norm" — company-wide, per-pipeline, or per-rep?
- Does rep coaching comparison need its own permission separate from the single `view` permission?
