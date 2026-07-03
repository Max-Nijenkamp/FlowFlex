---
domain: hr
module: dei-metrics
feature: dei-dashboard-aggregates
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: DEI dashboard (aggregates)

## Purpose

Present representation, pay-equity, hiring, and promotion equity to HR leadership using aggregate-only figures.

## Behavior

- `DeiDashboardPage` (#6 dashboard page, [[../../../../architecture/patterns/custom-pages]]) renders snapshot-driven charts.
- Reads `hr_dei_snapshots` only — never live decrypt-and-group over individuals.
- Groups below the suppression threshold show an "insufficient group size" placeholder.
- Pay-equity section uses `salary_band`, never exact salaries; hidden without hr.compensation. Hiring-funnel section hidden without hr.recruitment.

## Tables / Permissions

- Reads `hr_dei_snapshots`.
- Permission: `hr.dei.view-dashboard` (HR leadership). Gate: `canAccess()` also checks `BillingService::hasModule('hr.dei')`.

## UI

- **Kind**: custom-page (dashboard; hosts snapshot-driven chart widgets)
- **Page**: "DEI Dashboard" (`/hr/dei-dashboard`) — `DeiDashboardPage`
- **Layout**: representation charts (composition by level/department/role), a pay-equity section (band-level only, hidden without `hr.compensation`), and a hiring/promotion-equity section (hidden without `hr.recruitment`); cells for groups below the k-anonymity threshold render an "insufficient group size" placeholder instead of a value
- **Key interactions**: pick period/dimension to view a snapshot; read aggregate charts; no drill-down to individuals is ever possible
- **States**: empty = "No snapshot for this period yet — run the quarterly job" · loading = skeleton charts · error = "Couldn't load snapshot" with retry · selected = chosen dimension/period snapshot rendered · suppressed = small groups show "insufficient group size" placeholder · degraded = pay-equity/hiring sections hidden when their soft-dep module is inactive
- **Gating**: visible with `hr.dei.view-dashboard` (HR leadership) and `canAccess()` also checks `BillingService::hasModule('hr.dei')`

## Data

- Owns / writes: none — read-only over `hr_dei_snapshots` (own module table)
- Reads: `hr_dei_snapshots` only (aggregated, pre-suppressed) — never live decrypt-and-group over individuals; `salary_band` from `hr.compensation` for pay-equity *(assumed)*
- Cross-domain writes: none ([[../../../../security/data-ownership]])

## Relations

- Consumes: none (reads own snapshots; pay-equity/hiring sections read `hr.compensation` / `hr.recruitment` read APIs, `*(assumed)*`)
- Feeds: none outbound (privacy — DEI aggregates stay in the module)
- Shared entity: `hr_dei_snapshots` (own), band-level compensation + recruitment funnel data (read-only, soft-dep)

## Test Checklist

### Unit
- [ ] A snapshot group flagged suppressed maps to an "insufficient group size" placeholder, not a number
- [ ] Pay-equity uses `salary_band` only — never an exact salary value

### Feature (Pest)
- [ ] Dashboard reads `hr_dei_snapshots` only — no code path live-decrypts `hr_dei_attributes` at request time
- [ ] Pay-equity section hidden without `hr.compensation`; hiring/promotion section hidden without `hr.recruitment`
- [ ] Tenant isolation: company A's dashboard renders only company A snapshots

### Livewire
- [ ] `canAccess()` denies without `hr.dei.view-dashboard` or when `hr.dei` module inactive
- [ ] Suppressed groups render the placeholder; empty period shows the "run the quarterly job" first-use state

## Related

- [[../_module]]
- [[../architecture]]
- [[../security]]
