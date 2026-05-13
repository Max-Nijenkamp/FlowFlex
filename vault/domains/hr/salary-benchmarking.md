---
type: module
domain: HR & People
panel: hr
module-key: hr.salary-benchmarking
status: planned
color: "#4ADE80"
---

# Salary Benchmarking

> Market salary data by role and location, pay band building, comp review cycles, and merit simulation — everything HR needs to pay fairly and stay competitive.

**Panel:** `/hr`
**Module key:** `hr.salary-benchmarking`

## What It Does

Salary Benchmarking gives HR teams the data and tooling to build defensible, market-aligned pay bands without needing a compensation consultant. The module ingests external market salary data (via CSV import or partner integration), lets HR build min/mid/max bands per role and level, runs structured comp review cycles with per-employee recommended increases, calculates every employee's compa-ratio against their band midpoint, and simulates the cost of bringing underpaid employees up to band minimum before any budget decision is made.

## Features

### Core
- Pay band builder: define salary bands (min / midpoint / max) per job title, job level, location region, and currency with an effective date so historical bands are preserved
- Compa-ratio calculation: automatically computed per employee as actual salary ÷ band midpoint — surfaced on the employee profile and in the band analysis view
- Market data CSV import: upload a spreadsheet of market salary percentiles (P25, P50, P75, P90) per role per location; the module maps columns to band fields on import
- Band comparison view: table showing each band alongside its imported market P50 and P75, with a visual delta so HR can see at a glance which bands are below market
- Comp review cycle creation: open a cycle with a name, start/end date, and percentage budget cap per headcount

### Advanced
- Cycle entry management: per-employee rows in a cycle showing current salary, compa-ratio, recommended increase %, and approved increase % — HR managers enter recommendations; CHRO approves
- Budget tracker widget: real-time display of total budget allocated vs remaining across all employees in the active cycle — updates as managers enter recommendations
- Merit increase simulation: before approving a cycle, run a simulation that shows the payroll cost of bringing every employee below band minimum up to band minimum, separately from merit increases
- Pay equity analysis: within a band, show mean salary by gender and ethnicity (where data is available) with a % gap to band midpoint — flags statistically significant gaps automatically
- Salary range publication: for pay transparency, mark individual bands as published — published bands feed the Pay Transparency module
- Multi-currency support: bands can be defined in any currency; compa-ratio comparisons normalise to the company's base currency for portfolio views

### AI-Powered
- Band recalibration alerts: when new market data is imported, AI compares new P50 values to existing band midpoints and flags bands that are more than 10% below market — recommended adjustments are surfaced as a prioritised list
- Merit recommendation engine: given compa-ratio, performance rating (from Performance Reviews module), and time since last increase, AI suggests an increase % per employee within the cycle budget — HR can accept, adjust, or override
- Pay equity narrative: AI generates a plain-English summary of the pay equity analysis suitable for inclusion in a board report or DEI report

## Data Model

```erDiagram
    hr_salary_bands {
        ulid id PK
        ulid company_id FK
        string job_title
        enum job_level
        string currency
        decimal band_min
        decimal band_mid
        decimal band_max
        string location_region
        date effective_date
        enum data_source
        timestamps created_at/updated_at
    }

    hr_comp_review_cycles {
        ulid id PK
        ulid company_id FK
        string name
        date start_date
        date end_date
        decimal budget_percent
        enum status
        timestamps created_at/updated_at
    }

    hr_comp_review_entries {
        ulid id PK
        ulid cycle_id FK
        ulid employee_id FK
        decimal current_salary
        decimal recommended_increase_percent
        decimal approved_increase_percent
        ulid approved_by FK
        text notes
        timestamps created_at/updated_at
    }

    hr_market_data_imports {
        ulid id PK
        ulid company_id FK
        string source_name
        date effective_date
        string file_path
        integer rows_imported
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `hr_salary_bands.job_level` | enum: `junior` / `mid` / `senior` / `staff` / `principal` / `manager` / `director` / `vp` / `c-level` |
| `hr_salary_bands.data_source` | enum: `manual` / `csv_import` / `radford` / `mercer` / `levels_fyi` |
| `hr_salary_bands.effective_date` | Allows historical band snapshots — always query the latest effective date ≤ today |
| `hr_comp_review_cycles.status` | enum: `draft` / `open` / `approval` / `closed` |
| `hr_comp_review_entries.approved_increase_percent` | Null until CHRO approves; cycle cannot close while any entry remains null |
| `compa_ratio` | Computed at query time: `employee.salary ÷ band.band_mid` — not stored |

## Permissions

```
hr.salary-benchmarking.view-bands
hr.salary-benchmarking.manage-bands
hr.salary-benchmarking.view-salaries
hr.salary-benchmarking.manage-cycles
hr.salary-benchmarking.approve-cycle
```

## Filament

- **Resources:** `SalaryBandResource` (full CRUD — list, create, edit, delete bands with filters for job level, location, effective date)
- **Custom pages:** `CompReviewCyclePage` — a custom Filament page for the active comp review cycle: a Livewire table of all employees with inline editable recommended increase % field, a sticky `BudgetTrackerWidget` at the top showing total allocated vs remaining budget, and a simulation panel for below-minimum cost modelling
- **Widgets:** `CompaRatioDistributionWidget` (histogram of all employee compa-ratios — shows how many are below 0.8, 0.8–1.0, 1.0–1.2, above 1.2), `BudgetTrackerWidget` (inline on cycle page)
- **Nav group:** Payroll (hr panel)
- **Import:** custom Filament Import action on `SalaryBandResource` for market data CSV with column mapping step

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Pave | Compensation benchmarking, pay band builder, compa-ratio tracking |
| Levels.fyi (internal use) | Market salary data reference — replaced by structured import |
| Mercer Companalyst | Market data comparison and band calibration |
| Radford (Aon) | Compensation survey data and merit cycle management |
| Compa | Compensation management and cycle tooling |

## Related

- [[compensation-benefits]]
- [[pay-transparency]]
- [[performance-reviews]]
- [[dei-metrics]]
- [[employee-profiles]]
- [[payroll]]

## Implementation Notes

### External Salary Data Sources
Market salary data APIs have high access barriers — Radford and Mercer require enterprise contracts worth $50k+/year. Levels.fyi has no public API (data is scraped, not licensable). **Recommended approach for V1:** manual CSV import with a configurable column-mapping UI. The `data_source` enum allows future partner integrations to be added without schema changes.

For future partner integrations, the priority order is:
1. **Radford (Aon)** — best coverage for tech roles; API partner programme available
2. **Mercer** — strongest for non-tech and global roles
3. **Levels.fyi** — TC data for software engineers; pursue licensing or scrape-free API if available

The CSV import must accept at minimum: job title, job level, location, P25, P50, P75, P90, currency, effective date. Document the expected template in the onboarding wizard.

### Compa-Ratio Computation
Compa-ratio is computed at query time and never stored to avoid stale cached values. When the band changes (new effective date) or the employee salary changes, the ratio updates automatically. Surface compa-ratio on:
- Employee profile (single number with colour: green ≥0.9, amber 0.7–0.9, red <0.7)
- Band analysis scatter chart (all employees plotted vs band min/mid/max)
- Comp review cycle entry rows

### Merit Simulation
The simulation for "cost to bring all below-minimum employees to band minimum" must be clearly labelled as separate from the merit cycle budget. It shows: number of employees below minimum, total annual cost to remediate, % of payroll. This is informational — it does not auto-create cycle entries.
