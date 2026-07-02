---
domain: workplace
module: maintenance
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance

Report and track facility maintenance requests (broken AC, lighting, cleaning) with assignment, SLA, and resolution.

## Module-key

| Field | Value |
|---|---|
| key | `workplace.maintenance` |
| priority | p3 |
| panel | workplace |
| permission-prefix | `workplace.maintenance` |
| tables | `wp_maintenance_requests`, `wp_maintenance_schedules` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|core.files]] | before/after photos |
| Hard | [[../../core/notifications/_module\|core.notifications]] | assignment + resolution notifications |

## Core Features

- **Report a request** — location, category, description, priority, photo, reporter (any user). See [[features/report-request|Report a Request]].
- **Assignment + status machine** — `reported → assigned → in_progress → resolved → closed`; assign to staff or external contractor. See [[features/assignment-workflow|Assignment & Workflow]].
- **Priority + SLA** — resolution target days per priority, overdue flag *(assumed)*. See [[features/sla-tracking|SLA Tracking]].
- **Preventive schedules** — recurring maintenance auto-creating requests at `next_due_at`. See [[features/preventive-schedules|Preventive Schedules]].

## See features/

- [[features/report-request|Report a Request]] · [[features/assignment-workflow|Assignment & Workflow]] · [[features/sla-tracking|SLA Tracking]] · [[features/preventive-schedules|Preventive Schedules]]

## Build Manifest

```
database/migrations/xxxx_create_wp_maintenance_requests_table.php
database/migrations/xxxx_create_wp_maintenance_schedules_table.php
app/Models/Workplace/{MaintenanceRequest,MaintenanceSchedule}.php
app/States/Workplace/MaintenanceRequest/{MaintenanceState,Reported,Assigned,InProgress,Resolved,Closed}.php
app/Data/Workplace/{ReportMaintenanceData,CreateScheduleData}.php
app/Actions/Workplace/{AssignMaintenanceAction,ResolveMaintenanceAction}.php
app/Console/Commands/Workplace/RunMaintenanceSchedulesCommand.php
app/Filament/Workplace/Resources/{MaintenanceRequestResource,MaintenanceScheduleResource}.php
database/factories/Workplace/MaintenanceRequestFactory.php
tests/Feature/Workplace/MaintenanceTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Reporter sees own requests; staff sees all.
- [ ] Transitions per machine; resolve notifies reporter.
- [ ] Schedule run creates request once + advances `next_due_at`.
- [ ] Overdue flag per priority SLA.
- [ ] Photos tenant-scoped (image MIME, size cap, `companies/{id}/maintenance/`).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none confirmed)* | — | No cross-domain event specified *(assumed)*. A `MaintenanceReported`/`Resolved` event could feed finance (contractor cost) or assets — undecided ([[unknowns]]). |
| Reads | reporter / assignee | users, hr.profiles | staff resolved read-only |
| Commands | photo storage | core.files | Media Library, tenant-scoped |
| Commands | notifications | core.notifications | assignee + reporter pings |

**Data ownership:** `workplace.maintenance` writes only `wp_maintenance_requests` + `wp_maintenance_schedules`. Photos go through `core.files`; notifications through `core.notifications`. No other domain's tables are written ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../architecture/patterns/states]] · [[../room-booking/_module|Room Booking]]
