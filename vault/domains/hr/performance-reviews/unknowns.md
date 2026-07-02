---
domain: hr
module: performance-reviews
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Performance Reviews — Unknowns

Assumptions and open questions carried from the source spec. Each `*(assumed)*` is an authoritative default until overridden by ADR.

## Assumptions

- **Visibility rules** — employee sees own reviews after cycle finalised; manager sees reports' reviews; HR sees all. *(assumed)*
- **Rating scale default** — `hr_review_cycles.rating_scale` defaults to a 1–5 label set. *(assumed)*
- **Review content shape** — `hr_reviews.content` holds answers per question, with the question set defined on the cycle. *(assumed)*
- **Peer selection** — on activation, peers are chosen by the manager (self + manager reviews are auto-generated per employee). *(assumed)*
- **Calibration note** — a note is required when a rating is changed during calibration (`CalibrateRatingData.note`). *(assumed)*
- **MyGoalsPage placement** — the employee goal-progress custom page lives with the self-service nav. *(assumed)*
- **Peer anonymity** — the reviewee never sees the peer reviewer's identity. *(assumed)*

## Open Questions

- None recorded in the source spec beyond the assumptions above.

## Unverified

- Whole module is a rebuild blueprint; no code exists ([[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Table shapes, DTO fields, state transitions, and permissions are unverified against any implementation.

## Related

- [[_module]]
