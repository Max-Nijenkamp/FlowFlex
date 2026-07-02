---
domain: marketing
module: marketing-analytics
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Marketing Analytics — Unknowns

Parent: [[_module]]

## Assumed Items

- Dashboard polls every 60s *(assumed)*; current-window cache 15 min.
- CSV is the only export format v1 *(assumed)* — PDF/scheduled-email deferred.

## Open Questions

- Cross-channel attribution reconciliation (avoid double-counting across email + landing + forms) — the platform-bias problem competitors struggle with (see [[../_opportunities]]). Which single-source-of-truth model does the dashboard present?
- Whether analytics should live here or roll up into the platform-wide [[../../analytics/_index|Analytics]] domain — potential consolidation.
- Real-time vs. batch: is 60s polling enough, or is a materialised nightly roll-up needed at scale?

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
