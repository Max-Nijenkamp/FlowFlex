---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 6
status: complete
migration_range: 938000–941999
last_updated: 2026-05-12
---

# Net Zero Roadmap

Science-based target setting, reduction plan management, initiative tracking, and progress monitoring toward net zero commitments.

---

## Science-Based Targets (SBTi)

SBTi (Science Based Targets initiative) sets a standard for what counts as a credible net zero target:
- Near-term target: 50% reduction of Scope 1+2 by 2030 (from base year)
- Long-term target: 90% reduction by 2050 (net zero)
- Residual emissions (≤10%): neutralised with high-quality carbon removal

FlowFlex stores and tracks:
- Target commitments (uploaded or manually entered)
- Base year emissions (from [[carbon-footprint-tracking]])
- Annual actual emissions vs reduction pathway
- Progress to SBTi validation (submitted / validated / committed)

---

## Reduction Pathway

Visualisation of target trajectory:
- Linear or sectoral decarbonisation approach (SDA) pathway shown as dotted line
- Actual annual emissions plotted as solid line
- On-track / off-track status per year

The gap between actual and required = action needed for next year.

---

## Reduction Initiatives

Structured plan for how the company will reduce emissions:

| Initiative | Scope | Expected Reduction | Target Year | Status |
|---|---|---|---|---|
| Switch to 100% renewable electricity (RE100) | Scope 2 | 450 tCO₂e / year | 2026 | In progress |
| EV fleet transition | Scope 1 | 280 tCO₂e / year | 2027 | Planned |
| Supplier engagement programme | Scope 3 | 1,200 tCO₂e / year | 2028 | Planned |
| Office consolidation (hybrid policy) | Scope 1+2 | 90 tCO₂e / year | 2026 | Complete |

Each initiative tracked with:
- Owner (employee)
- Budget allocated
- Actual reduction achieved (reported annually)
- Evidence attachment

---

## Data Model

### `esg_net_zero_targets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| framework | varchar(100) | "SBTi" / "CDP" / "Company-internal" |
| target_type | enum | near_term/long_term |
| base_year | int | |
| base_year_emissions | decimal(14,2) | tCO₂e |
| target_year | int | |
| target_reduction_pct | decimal(5,2) | |
| target_emissions | decimal(14,2) | computed |
| status | enum | draft/committed/submitted/validated |
| validation_date | date | nullable |

### `esg_reduction_initiatives`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| title | varchar(300) | |
| description | text | |
| scope_coverage | json | ["scope1", "scope2"] |
| expected_reduction_tco2e | decimal(10,2) | per year at target |
| budget | decimal(14,2) | nullable |
| owner_id | ulid | FK `employees` |
| target_year | int | |
| status | enum | planned/in_progress/complete/cancelled |
| actual_reduction_tco2e | decimal(10,2) | nullable, reported when complete |

---

## Integrations

- **Carbon Footprint Tracking** — actual annual emissions as progress input
- **Finance** — initiative budgets linked to cost centres
- **ESG Report Builder** — net zero roadmap section in CSRD report

---

## Migration

```
938000_create_esg_net_zero_targets_table
938001_create_esg_reduction_initiatives_table
938002_create_esg_progress_snapshots_table
```

---

## Related

- [[MOC_ESG]]
- [[carbon-footprint-tracking]] — emission data source
- [[esg-report-builder]] — net zero section
- [[supply-chain-sustainability]] — Scope 3 reduction via supplier programme
