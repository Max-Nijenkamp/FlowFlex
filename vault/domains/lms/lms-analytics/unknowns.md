---
domain: lms
module: lms-analytics
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# LMS Analytics — Unknowns

## Assumed Items

- Live aggregation + cache (no materialised reporting table) in v1 *(assumed)*.
- Cache TTLs 1h/15min *(assumed)*.
- "Avg time spent" derives from lesson progress timestamps *(assumed)* — no explicit time-on-task tracking table exists.

## Open Questions

- Does engagement need real time-on-task tracking (a new table) rather than inferring from progress timestamps?
- Should LMS metrics feed the cross-domain [[../../analytics/_index|Analytics domain]] as a data source, or stay panel-local?
- Export format(s) — CSV / Excel / PDF — and whether exports are queued (`maatwebsite/laravel-excel`).
- Should compliance % feed an HR / audit report cross-domain (regulator-facing)?
- Drop-off analysis granularity — per lesson, per quiz question, or per video timestamp?
