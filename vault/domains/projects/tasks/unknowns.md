---
domain: projects
module: tasks
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks — Unknowns

## Assumed Items

- `completed_at` column present *(assumed)*.
- Comments plain-first, rich text purified *(assumed v1)*.
- Done-with-open-blockers is warn-not-block, configurable *(assumed)*.
- Audit is status-only lightweight *(assumed)*.

## Open Questions

- Full-text search on task title/description (Meilisearch/Scout) — in v1 or later?
- Recurring tasks — supported, or out of scope for v1?
- Task templates vs project-template tasks — any overlap?
- Should `blocks` dependencies auto-notify the blocking task's assignee on unblock?
