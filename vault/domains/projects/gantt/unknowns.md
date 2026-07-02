---
domain: projects
module: gantt
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — Unknowns

## Assumed Items

- Task bar start = due − estimated days when no explicit start exists *(assumed)*.
- 60s polling for freshness (not broadcast) *(assumed)*.
- Export PNG/PDF is client-side render *(assumed)*.
- JS lib is `frappe-gantt` (or similar), bundled via vite *(assumed)*.

## Open Questions

- Do tasks need an explicit `start_date` column so bars aren't inferred from estimate?
- Should Gantt subscribe to `TaskMoved` for live updates instead of polling?
- Baseline vs actual timeline comparison — v1 or later?
