---
domain: workplace
module: workplace-analytics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics — Unknowns

## Assumed Items

- Visitor aggregation reads counts/volumes only, not decrypted PII *(assumed)*.
- Cache TTL 1 h historical / 15 min current *(assumed)*.
- Export throttled per user *(assumed rate)*.
- Peak-hours + weekday-distribution granularity *(assumed — hourly / per-weekday)*.

## Open Questions

- Should metrics be materialised into a projection table for very large tenants (vs on-demand compute)?
- Which export format(s) — CSV, PDF, both?
- Should analytics consume booking/no-show **events** to keep a live projection, rather than re-querying (ties to the undecided `RoomBooked` / `DeskBooked` events)?
- Space-optimisation thresholds (what counts as "underused") — fixed or configurable?
- Any cost/spend rollup once maintenance tracks cost?
