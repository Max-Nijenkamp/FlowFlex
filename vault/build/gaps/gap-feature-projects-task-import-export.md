---
type: gap
severity: medium
category: feature
status: accepted
domain: projects
color: "#F97316"
discovered: 2026-07-03
discovered-in: projects.tasks
---

# Task CSV/Excel import + export missing (migrate-off on-ramp)

## Context
`projects.tasks` specs task CRUD, subtasks, dependencies, comments, and a My Tasks page, but **no
task-level spreadsheet import or export**. Import/export in the Projects domain only exists for time
tracking (`time-report-export`) and Gantt — not for the tasks that hold the actual work. HR profiles
already leans on a soft `core.data-import` dependency for bulk records; tasks has no equivalent.

## Problem
Teams migrating off Asana / Monday / ClickUp / Jira arrive with a spreadsheet of tasks and expect to
bulk-load them. ClickUp users cite ClickUp↔ClickUp import/export as "one of the biggest needs" of the
platform. Without a task importer there is no clean migration on-ramp, and no bulk export for reporting
or backup. This mirrors the already-logged `gap-feature-marketing-subscriber-import` and
`gap-feature-events-attendee-import` on-ramp gaps.

## Impact
- Blocks the "switch to FlowFlex" migration story for the Projects panel (a Phase-2 differentiator).
- Manual task re-entry for onboarding customers; no bulk export for stakeholders.

## Proposed Solution
Add a task importer + exporter using the **already-chosen** `maatwebsite/laravel-excel` (import) and
`pxlrbt/filament-excel` (export) — no new packages. Import maps columns to task fields with per-row
try/catch results (mirror the LMS `BulkEnrolData` pattern) and mutates only through the tasks module's
actions ([[../../security/data-ownership]]). Column set + dependency/section resolution to be specced in a
`features/task-import-export` note.

## Related
- [[../../domains/projects/tasks/_module]] · [[../../domains/projects/_opportunities]]
- Parallels: [[gap-feature-marketing-subscriber-import]] · [[gap-feature-events-attendee-import]]
