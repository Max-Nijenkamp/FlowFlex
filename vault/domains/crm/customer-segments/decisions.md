---
domain: crm
module: customer-segments
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Segments — Decisions

## ADR: Dynamic vs static segments

Two segment types are supported. Dynamic segments store a `conditions` rule tree and resolve membership at read time; static segments store an explicit member list in `crm_segment_members`. Static exists for manually curated audiences that shouldn't drift with data changes.

## ADR: Query-time membership, not materialised

Dynamic membership is resolved as a scoped query at read time and never materialised into a members table. This guarantees membership always reflects current data. `member_count` is the only cached artefact — a nightly snapshot, not authoritative for resolution. *(assumed: member_count cached snapshot only)*

## ADR: One nesting level for AND/OR

Condition groups combine with a single AND/OR logic level; arbitrary nested boolean trees are out of scope for v1. *(assumed)*

## ADR: Single audience API

`SegmentService::contacts()` is the sole entry point for resolving a segment's audience. Marketing campaigns, CRM sequences, and broadcasts all call it rather than reimplementing filtering, keeping tenant scoping and condition semantics in one place.

## Related

- [[../../../architecture/patterns/custom-fields]]
- [[unknowns]]
