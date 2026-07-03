---
domain: projects
module: okrs
feature: checkins-dashboard
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Check-ins & Dashboard

Periodic KR check-ins, roll-up health dashboard, and reminder notifications.

## Behaviour

- Check-in on a KR: new `current_value` + notes → recompute KR + cascade objective/parent progress.
- Health = progress vs quarter elapsed (on-track / at-risk / off-track *(assumed)*).
- Weekly reminder to owners of KRs stale >7 days (`OkrCheckinReminderCommand`).

## UI

- **Kind**: custom-page (dashboard) + a check-in action on the resource.
- **Page**: `OkrDashboardPage` at `/app/projects/okrs/dashboard` (nav group OKRs); check-in is a KR row action.
- **Layout**: quarter selector; health distribution donut; per-objective progress list; recent check-ins feed.
- **Key interactions**: quarter switch → recompute view; check-in modal (value + notes); click objective → detail.
- **States**: empty (no OKRs this quarter → "Set your first objective") · loading (skeleton cards) · error (unauthorised check-in → toast) · selected (objective highlighted).
- **Gating**: view `projects.okrs.view-any`; check-in own `update-own`, others `update-any`.

## Data

- Owns / writes: `proj_okr_checkins` (+ recomputed `progress_percent` on objectives/KRs).
- Reads: own OKR tree.
- Cross-domain writes: none — reminders sent via `NotificationService` API ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `NotificationService::notify` (reminders) → core.notifications.
- Shared entity: `users` (owners).

## Test Checklist

### Unit
- [ ] KR progress recomputes with baseline and clamps 0–100 from a submitted check-in value.
- [ ] Health classification (on-track / at-risk / off-track) at the boundary thresholds vs quarter time-elapsed.

### Feature (Pest)
- [ ] Check-in writes a `proj_okr_checkins` row and cascades objective + parent `progress_percent` (real sqlite).
- [ ] Non-owner check-in without `projects.okrs.update-any` is rejected; owner with `update-own` succeeds.
- [ ] Weekly reminder targets only KRs stale >7 days, tenant-scoped per company; concurrent check-ins on sibling KRs don't clobber the objective roll-up (lockForUpdate).

### Livewire
- [ ] `OkrDashboardPage` denied without `projects.okrs.view-any`; hidden when `projects.okrs` inactive.
- [ ] Check-in modal submits value + notes and refreshes the health-distribution donut.

## Unknowns

- Confidence self-rating; auto-pulled KR values from other domains — see [[../unknowns]].

## Related

- [[../_module|OKRs]] · [[objectives-key-results|Objectives & KRs]] · [[../../../core/notifications/_module|Notifications]]
