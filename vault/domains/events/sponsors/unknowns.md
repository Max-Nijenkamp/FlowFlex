---
domain: events
module: sponsors
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — Unknowns

## Assumed Items

- Sponsor `status` is committed/paid *(assumed)* — no richer lifecycle (pledged/invoiced/overdue) specified.
- Tiers are a fixed set (platinum/gold/silver/bronze) *(assumed)* — no per-company config in v1.
- The Finance invoice link is a manual action *(assumed)* — no automatic revenue posting.
- The deliverable reminder is one-shot via a `reminded` flag *(assumed)*.

## Open Questions

- Should paying a sponsor invoice (Finance side) auto-flip the sponsor `status` to `paid` (via a Finance event), or stay manual?
- Can a deliverable be re-reminded (flag reset) when a new due date is set?
- Sponsor ROI metrics (lead scans, booth traffic) — is there a sponsor-facing report, tying into [[../event-analytics/_module|Event Analytics]]? (Competitor gap — see [[../_opportunities]].)
- Per-company custom tier definitions — needed for larger organizers?
