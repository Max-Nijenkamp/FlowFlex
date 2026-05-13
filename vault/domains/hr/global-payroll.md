---
type: module
domain: HR & People
panel: hr
module-key: hr.global-payroll
status: planned
color: "#4ADE80"
---

# Global Payroll

> Multi-country payroll record tracking — record payroll figures across different countries and currencies, track local deductions, and consolidate employer cost reporting.

**Panel:** `hr`
**Module key:** `hr.global-payroll`

## What It Does

Global Payroll extends the domestic Payroll module to handle employees paid in different countries and currencies. Each payroll entry can be denominated in the employee's local currency and converted to the company's base currency for consolidated reporting. Country-specific deduction profiles capture local tax, pension, and social security structures. The module does not process payroll or submit to tax authorities — it records and tracks. Companies using EOR (Employer of Record) providers or local payroll bureaus enter their bureau's output data into FlowFlex for consolidated visibility.

## Features

### Core
- Country profiles: configure supported countries with currency, deduction types (income tax, social security, pension), and payroll frequency
- Multi-currency payroll entries: each entry records local currency gross, deductions, and net pay — converted to base currency at the entry exchange rate
- Exchange rate management: manually enter monthly exchange rates per currency pair or integrate with open exchange rate API
- Consolidated payroll report: total employer cost across all countries in base currency — one view of the global headcount cost

### Advanced
- EOR tracking: flag employees managed by an Employer of Record provider — record the EOR fee separately as an employer cost
- Country compliance notes: per-country notes field for payroll administrators to capture local regulatory requirements or exceptions
- Variance report: compare this month's global payroll to prior month — highlight changes above threshold per country
- Currency exposure report: total payroll liability per currency — used by Finance for FX hedging decisions
- Payroll consolidation with domestic: global employees included in company-wide headcount and cost analytics alongside domestic employees

### AI-Powered
- FX impact modelling: if a currency moves 5%+ against base currency, AI estimates the impact on next month's global payroll cost and surfaces a notification to Finance
- Compliance risk flags: based on country configurations, flag missing deduction types that are mandatory per jurisdiction

## Data Model

```erDiagram
    global_payroll_countries {
        ulid id PK
        ulid company_id FK
        string country_code
        string currency
        string payroll_frequency
        json deduction_types
        timestamps created_at/updated_at
    }

    global_payroll_entries {
        ulid id PK
        ulid payroll_run_id FK
        ulid employee_id FK
        ulid company_id FK
        string country_code
        string local_currency
        decimal local_gross
        decimal local_deductions
        decimal local_net
        decimal fx_rate
        decimal base_gross
        decimal base_net
        boolean is_eor
        decimal eor_fee
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `country_code` | ISO 3166-1 alpha-2 (e.g. NL, DE, GB, US) |
| `fx_rate` | Exchange rate local → base currency at run date |
| `is_eor` | True for employees via Employer of Record |

## Permissions

- `hr.global-payroll.view`
- `hr.global-payroll.create-run`
- `hr.global-payroll.manage-countries`
- `hr.global-payroll.view-consolidated`
- `hr.global-payroll.export`

## Filament

- **Resource:** `GlobalPayrollCountryResource`
- **Pages:** `ListGlobalPayrollRuns`, `GlobalPayrollConsolidatedPage` — multi-currency cost dashboard
- **Custom pages:** `GlobalPayrollConsolidatedPage`
- **Widgets:** `GlobalHeadcountCostWidget` — total employer cost in base currency across all countries
- **Nav group:** Payroll (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Deel | Global payroll tracking |
| Remote | Multi-country payroll management |
| Rippling | Global payroll records |
| Papaya Global | International payroll consolidation |

## Implementation Notes

**Scope clarification:** Like the domestic payroll module, this is a **record-keeping and consolidation** module — not a payroll processor. Companies using EOR providers (Deel, Remote, Papaya Global) enter the EOR's output figures into FlowFlex for consolidated visibility. FlowFlex does not integrate with Deel/Remote APIs to pull payroll data automatically in this phase. If API integration with EOR providers is required, it should be a separate integration module.

**Exchange rate dependency:** `global_payroll_entries.fx_rate` must be populated at the time the run is created. The rate comes from `exchange_rates` in the multi-currency module — query `SELECT rate FROM exchange_rates WHERE company_id = ? AND from_currency = ? AND to_currency = ? AND rate_date <= ? ORDER BY rate_date DESC LIMIT 1`. If `exchange_rates` has no entry for the currency pair on the run date, the system must warn and require manual rate entry before the run can be saved.

**Filament:** `GlobalPayrollConsolidatedPage` is a custom `Page` — not a standard Resource list. It renders a summary table of all active countries with current-period employer costs in both local currency and base currency, plus a total row. Uses a chart.js bar chart for visual cost comparison across countries. The page is listed both as a Resource page and a Custom page in the spec — this is a conflict. The correct structure is: `GlobalPayrollCountryResource` (standard CRUD for country configs) + `GlobalPayrollConsolidatedPage` (custom Page for the consolidated cost view).

**Missing from data model:** `global_payroll_entries.payroll_run_id` references a `payroll_runs` table from the domestic payroll module. For global payroll, the run tracking needs to be clear: are global payroll entries part of the same `payroll_runs` table (with a `type: global|domestic` discriminator) or does global payroll have its own `global_payroll_runs` table? Recommend a `global_payroll_runs` table: `{ulid id, ulid company_id, string name, date period_start, date period_end, string status, timestamps}` to avoid coupling to domestic payroll.

**AI features:** FX impact modelling is a PHP-only calculation (headcount × current rate vs headcount × projected rate). No LLM required. Compliance risk flags are rule-based — check `global_payroll_countries.deduction_types` JSON against a hardcoded registry of required deduction types per country code.

## Related

- [[payroll]]
- [[employee-profiles]]
- [[hr-analytics]]
- [[compensation-benefits]]
