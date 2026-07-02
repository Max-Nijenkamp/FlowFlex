---
domain: analytics
module: dashboards
feature: dashboard-sharing
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Dashboard Sharing

Share a dashboard read-only with the team, or keep it private — intra-company only.

## Behaviour

- `is_shared = false` (default) → visible only to the owner.
- `is_shared = true` → visible read-only to same-company users with `analytics.dashboards.view-any`.
- Only the owner edits a dashboard's widgets/layout; a `manage-shared` holder can toggle sharing / manage team-shared dashboards.
- **Sharing never crosses companies** — a shared dashboard is scoped to its `company_id`.

## UI

- **Kind**: widget/action — a share toggle + a shared/private badge on [[dashboard-builder]] and in the dashboard list; no standalone page.
- **Page**: action on `DashboardResource` + the builder top bar.
- **Layout**: share toggle in the dashboard header; list column showing shared/private + owner.
- **Key interactions**: owner flips the share toggle → dashboard becomes team-visible read-only (optimistic + confirm); non-owner opening a shared dashboard sees a read-only canvas (no edit affordances).
- **States**: private (owner-only) · shared (team read-only) · error (toggle without permission → blocked) · selected (dashboard open, badge reflects state).
- **Gating**: view shared with `analytics.dashboards.view-any`; edit only by owner (`analytics.dashboards.update-own`); toggle sharing with `analytics.dashboards.manage-shared`.

## Data

- Owns / writes: `bi_dashboards.is_shared` (this module's table).
- Reads: current user + company for visibility scoping (CompanyScope).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: dashboards from [[dashboard-builder]]; rendered widgets from [[widget-rendering]].
- Feeds: nothing downstream — a shared dashboard is also a schedulable source for [[../../scheduled-exports/_module|analytics.exports]].
- Shared entity: same-company users (audience), owner user (by id).

## Test Checklist

### Unit
- [ ] Visibility rule: `is_shared=false` → owner-only; `is_shared=true` → same-company `view-any` holders, read-only.

### Feature (Pest)
- [ ] Owner toggles share on → same-company user with `view-any` can now open it read-only.
- [ ] Shared dashboard is never visible cross-company (scoped to `company_id`).
- [ ] Non-owner cannot edit widgets/layout of a shared dashboard.

### Livewire
- [ ] Share toggle requires `analytics.dashboards.manage-shared`; blocked otherwise.
- [ ] Non-owner opening a shared dashboard sees a read-only canvas (no edit affordances).

## Unknowns

- Whether `manage-shared` can edit someone else's shared dashboard or only toggle its sharing — see [[../unknowns]] Q3.

## Related

- [[../_module|Custom Dashboards]] · [[dashboard-builder]] · [[widget-rendering]] · [[../security]]
