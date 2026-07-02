---
domain: projects
module: projects
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — Unknowns

## Assumed Items

- `color` default drawn from a palette *(assumed)*.
- Completing an `active` project with open tasks shows a confirm modal *(assumed)*.
- Health thresholds: at-risk >15pt, off-track >30pt behind elapsed timeline *(assumed)*.
- Visibility model: projects are member-visible; `view-any` sees all *(assumed)*.

## Open Questions

- Should there be a **client-facing project portal** (Vue/Inertia) exposing status/milestones to the CRM contact?
- Is project **archive** a soft-delete or a distinct archived flag preserving list filters?
- Do budgets need multi-currency, or single company currency in v1?
- Should health be recomputed on a schedule (cache) or on-read?
