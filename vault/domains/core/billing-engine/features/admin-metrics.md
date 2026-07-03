---
domain: core
module: billing-engine
feature: admin-metrics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Admin Metrics (MRR / Churn / Adoption)

Parent: [[../_module]] · See [[../architecture]] · [[../data-model]]

Revenue and adoption metrics for FlowFlex staff, surfaced in the `/admin` panel. Derived read-only from subscription and invoice data — this is a reporting surface, not a write path.

## Behaviour

- `BillingService::mrr(): Money` — monthly recurring revenue across all active subscriptions (brick/money).
- `BillingService::churnRate(CarbonImmutable $period): float` — churn for a period.
- Module adoption rate = active subscriptions per module ÷ eligible companies.
- All figures are computed on read from `company_module_subscriptions` + `billing_invoices`; nothing here mutates billing state.

## UI

- **Kind**: widget
- **Page**: Filament stat/chart widgets on the `/admin` billing dashboard (staff panel).
- **Layout**: stat cards (MRR, churn %, active companies) plus a per-module adoption bar/line chart (leandrocfe/filament-apex-charts).
- **Key interactions**: staff view the dashboard, switch the period selector; read-only, no edit actions.
- **States**: empty = pre-launch / no subscriptions ("no revenue data yet") · loading = widget skeleton · error = query failure shows a widget-level error placeholder · selected = a period chosen in the range selector.
- **Gating**: staff-only (`/admin` panel access); not exposed on `/app`. `core.billing.view` at minimum plus admin-panel membership.

## Data

- Owns / writes: nothing — read-only over `company_module_subscriptions`, `billing_invoices`, `billing_invoice_lines` (this module's own tables).
- Reads: only this module's tables; company count read-only via tenancy.
- Cross-domain writes: none — a pure reporting surface. See [[../../../../security/data-ownership]].

## Relations

- Consumes: none.
- Feeds: none (read-only metrics; not consumed by other domains).
- Shared entity: none beyond the billing tables it aggregates.

## Test Checklist

### Unit
- [ ] MRR = sum of active-subscription module prices via brick/money (no float math)
- [ ] Churn rate formula returns the expected ratio for a period *(assumed definition)*

### Feature (Pest)
- [ ] `mrr()` aggregates only active subscriptions across all companies (admin scope, not company-scoped)
- [ ] `churnRate($period)` computes correctly over seeded subscription/cancellation data

### Livewire
- [ ] Metrics widgets render on `/admin` only; denied on `/app` and to non-staff users

## Unknowns

- Exact churn formula and adoption denominator are not specified in the source notes — `*(assumed)*` reporting definitions. See [[../unknowns]].

## Related

- [[../_module|Billing Engine]] · [[monthly-invoicing]] · [[module-gating]] · [[../../staff-console/_module]]
