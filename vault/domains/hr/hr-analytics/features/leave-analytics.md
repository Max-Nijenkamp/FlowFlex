---
domain: hr
module: hr-analytics
feature: leave-analytics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Leave Analytics

## Purpose

Leave utilisation across the workforce. Soft-dependent on [[../../leave-management/_module]] (`hr.leave`).

## Behavior

Average days taken vs allocated per leave type, via `LeaveUtilisationWidget`. The widget is **hidden** when `hr.leave` is inactive.

## Source Data

`hr_leave_requests`. Aggregated in `HrAnalyticsService::metrics` → `leave_utilisation[]`.

## Permissions

`hr.analytics.view` + module gating (both `hr.analytics` and `hr.leave` active).

## UI

- **Kind**: widget
- **Page**: hosted on the "HR Analytics" dashboard (`/hr/analytics`) as `LeaveUtilisationWidget`
- **Layout**: bar/column chart of average days taken vs allocated per leave type; the whole widget is omitted from the dashboard grid when `hr.leave` is inactive
- **Key interactions**: change the header period filter to re-scope; hover a bar for taken/allocated tooltip per leave type
- **States**: empty = "No leave records for period" placeholder · loading = skeleton bars · error = "Couldn't load leave utilisation" with retry · selected = hovered bar shows exact taken vs allocated · hidden = widget absent entirely when `hr.leave` is not active (degraded soft-dep)
- **Gating**: visible with `hr.analytics.view` and both `hr.analytics` + `hr.leave` modules active

## Data

- Owns / writes: none — read-only aggregation
- Reads: `hr_leave_requests` via `hr.leave` read API; aggregated in `HrAnalyticsService::metrics` → `leave_utilisation[]`
- Cross-domain writes: none — never writes another domain's tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: `LeaveRequestApproved` from `hr.leave` → refresh utilisation projection *(assumed — may recompute live per request)*
- Feeds: none (read-only dashboards)
- Shared entity: `hr_leave_requests` (read-only)

## Test Checklist

### Unit
- [ ] `leave_utilisation[]` = average days taken vs allocated per leave type, computed from fixtures

### Feature (Pest)
- [ ] Utilisation aggregated from `hr_leave_requests` is company-scoped
- [ ] Widget is hidden entirely when `hr.leave` is inactive (soft-dep degraded behavior)

### Livewire
- [ ] `LeaveUtilisationWidget` omitted from the dashboard grid when `hr.leave` inactive; visible + `canView()`-gated when both modules active

Parent: [[../_module]]
