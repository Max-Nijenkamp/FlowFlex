---
domain: crm
module: customer-segments
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Segments — Unknowns

## Assumptions

- *(assumed)* `member_count` is a cached snapshot only, refreshed nightly — not authoritative for dynamic resolution.
- *(assumed)* AND/OR conditions support one nesting level; no arbitrary nested boolean trees in v1.
- *(assumed set)* Allowed operators: `equals`, `not-equals`, `contains`, `gt`, `lt`, `in`, `has-tag`, `days-since-activity-gt`.

## Open Questions

- Should `member_count` refresh cadence be configurable per company, or is nightly sufficient?
- Do we need real-time count updates in the builder beyond the on-demand `preview()` call?
- How large can a static list grow before we need pagination/import tooling?
- Should overlap analysis be surfaced in the UI, or is it a service-only capability for now?
