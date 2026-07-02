---
domain: workplace
module: maintenance
feature: assignment-workflow
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Assignment & Workflow

Route a request through the status machine: assign, progress, resolve, close.

## Behaviour

- `reported → assigned` via `AssignMaintenanceAction` (staff or free-text contractor); assignee notified.
- `assigned → in_progress` by the assignee.
- `in_progress → resolved` via `ResolveMaintenanceAction`; reporter notified; after-photo prompt.
- `resolved → closed` by reporter confirm, or auto after 7 days *(assumed)*.
- Any open state can be reopened to `reported`.

## UI

- **Kind**: simple-resource (row actions + status column on the request resource)
- **Page**: assign/resolve/close as row + detail actions on `MaintenanceRequestResource`.
- **Layout**: status badge column; queue tabs; action buttons gated by state + permission.
- **Key interactions**: "Assign" (pick staff/contractor) → "Start" → "Resolve" (attach after-photo) → "Close"; illegal transitions hidden.
- **States**: empty (no requests in tab) · loading (action pending) · error (invalid transition / permission toast) · selected (request highlighted).
- **Gating**: assign `workplace.maintenance.assign`; resolve `workplace.maintenance.resolve`.

## Data

- Owns / writes: `wp_maintenance_requests` (`status`, `assignee_id`, `contractor`, `resolved_at`, `closed_at`) only.
- Reads: `users` / `hr.profiles` for assignees (read-only).
- Cross-domain writes: none — notifications via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: resolution-time metrics read by [[../../workplace-analytics/_module|Workplace Analytics]]. A `MaintenanceResolved` event is *(assumed)* / undecided ([[../unknowns]]).
- Shared entity: `hr_employees` (assignee) — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Related

- [[../_module|Facility Maintenance]] · [[report-request]] · [[sla-tracking]] · [[../architecture]]
