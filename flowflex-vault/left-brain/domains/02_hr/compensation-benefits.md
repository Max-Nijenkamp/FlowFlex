---
type: module
domain: HR & People Management
panel: hr
phase: 3
status: complete
cssclasses: domain-hr
migration_range: 107000–107499
last_updated: 2026-05-12
---

# Compensation & Benefits

Manage salary bands, benefit packages, equity grants, and total compensation statements. Ensures pay equity and gives employees transparency on the full value of their package.

---

## Salary Bands

Compensation framework:
- Job levels (L1–L6 or Junior/Mid/Senior/Staff/Principal)
- Pay bands per level per location (Amsterdam, London, Remote-EU)
- Band: min / midpoint / max
- Salary review cycle: annual or bi-annual

**Pay equity analysis**: flag employees below band minimum or above band maximum. Identify gender/ethnicity pay gaps across levels.

---

## Benefits Catalogue

Configurable benefit packages per employment type and country:
| Benefit | Country | Cost |
|---|---|---|
| Pension contribution | NL | 8% employer |
| Health insurance | NL/UK | €150/month |
| Commuter allowance | NL | €0.23/km |
| Home office budget | All | €500 one-time |
| Learning budget | All | €1,500/year |
| Company car / mobility | NL senior+ | By level |

---

## Total Compensation Statement

Annual "TCS" PDF per employee:
- Base salary
- Bonus (target and actual)
- Benefits value (pension, health, allowances)
- Equity (options granted × current 409A value)
- **Total Value**: €92,400

Increases appreciation of package beyond raw salary number.

---

## Equity / Options

For companies with equity plans:
- Options granted per employee: grant date, strike price, cliff, vesting schedule
- ISO / NSO / EMI / CSOP plan types
- Dilution tracking (total granted vs pool)
- Employee-facing: vested, unvested, current estimated value

---

## Bonus Management

Define bonus schemes:
- Annual performance bonus: target % of salary by level
- Commission plans (links to [[commission-management]] in CRM)
- Spot bonuses: one-off award (requires approval)
- Bonus accrual: monthly GL accrual → pay out annually

---

## Data Model

### `hr_salary_bands`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| job_level | varchar(50) | |
| location | varchar(100) | |
| currency | char(3) | |
| band_min | decimal(14,2) | |
| band_mid | decimal(14,2) | |
| band_max | decimal(14,2) | |

### `hr_employee_compensation`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| employee_id | ulid | FK |
| effective_date | date | |
| base_salary | decimal(14,2) | |
| currency | char(3) | |
| bonus_target_pct | decimal(5,2) | nullable |
| equity_options | int | nullable |

---

## Migration

```
107000_create_hr_salary_bands_table
107001_create_hr_employee_compensation_table
107002_create_hr_benefits_catalogue_table
107003_create_hr_equity_grants_table
```

---

## Related

- [[MOC_HR]]
- [[global-payroll]]
- [[performance-reviews-360]]
- [[headcount-planning]] (FP&A)
