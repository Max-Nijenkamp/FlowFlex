---
domain: crm
module: deal-rooms
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deal Rooms — Unknowns & Open Questions

## Assumptions

- `expires_at` defaults to the deal close date + 30 days *(assumed)*.
- Buyers cannot upload documents in v1 *(assumed)*; buyer-side writes are limited to toggling action items and logging views.
- A dedicated Q&A thread is deferred; v1 uses action items with comments *(assumed)*.

## Open Questions

- Should room expiry be configurable per-company, or always tied to the deal close date?
- When a deal is re-opened or its close date moves, does the room's `expires_at` follow automatically?
- Does buyer document upload (deferred) need its own permission and virus-scan pipeline?
- Should engagement events (views) also notify the deal owner in real time?
