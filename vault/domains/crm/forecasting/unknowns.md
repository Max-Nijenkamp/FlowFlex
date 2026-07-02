---
domain: crm
module: forecasting
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Unknowns & Open Questions

## Assumptions

- *(assumed)* `forecast_category` column is added to `crm_deals` by this module and owned here (commit / best-case / pipeline / closed).
- *(assumed)* Snapshot command queue is `default`.

## Open Questions

- Should team roll-up follow the RBAC role hierarchy or an explicit reporting/manager relationship on `users`?
- Multi-currency quotas: is attainment computed per currency, or converted to a company base currency (links [[../price-management/_module|Price Management]] / finance.currency)?
- Are quarterly and monthly quotas mutually exclusive per rep, or can both coexist?
