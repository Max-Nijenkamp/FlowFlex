---
domain: procurement
module: requisitions
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — Open Questions

- Over-budget: warn vs block. Currently warn `*(assumed)*`; a per-company "hard block" toggle is a candidate differentiator ([[../_opportunities]]).
- `department_id` source when HR inactive — free text vs required? `*(assumed: nullable)*`
- Requisition templates as a first-class entity vs duplicate action. `*(assumed: duplicate)*`
- Partial conversion (some lines → PO now, rest later) — out of scope v1? **UNVERIFIED**
- Category derivation for `chainFor` — from line categories or a header field? `*(assumed: header/first-line category)*`

## Related

- [[_module]] · [[decisions]]
