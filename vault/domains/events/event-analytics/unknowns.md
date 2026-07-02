---
domain: events
module: event-analytics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Event Analytics — Unknowns

## Assumed Items

- Landing-page **views** are a funnel input *(assumed)* — the source of the view count (a tracking table? a pixel?) is unspecified and no table is owned to store it.
- Session popularity uses overall attendance as a proxy *(assumed)* — no per-session check-in data exists yet.

## Open Questions

- Where do landing-page **views** come from? Without a view-tracking store, the top of the funnel is undefined — candidate: a lightweight owned `ev_event_views` counter, or an external analytics read.
- Should the export be scheduled/emailed (recurring reports) or on-demand only?
- Cross-event comparison scope — all events, a tag/segment, or a manual pick?
- Sponsor ROI depth — revenue-only, or lead-scan / booth-traffic metrics (a competitor differentiator, see [[../_opportunities]])?
