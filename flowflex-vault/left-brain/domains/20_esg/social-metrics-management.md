---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 5
status: complete
migration_range: 932000–934999
last_updated: 2026-05-12
---

# Social Metrics Management

Collect and report on the Social (S) pillar of ESG: diversity & inclusion, labour standards, pay equity, training hours, health & safety, supply chain social data.

---

## ESRS S Standards Coverage

Under CSRD, four ESRS Social standards apply:
| Standard | Topic |
|---|---|
| ESRS S1 | Own Workforce |
| ESRS S2 | Workers in value chain (supply chain) |
| ESRS S3 | Affected communities |
| ESRS S4 | Consumers and end-users |

This module primarily covers S1 (own workforce) with S2 via [[supply-chain-sustainability]] link.

---

## Data Points Collected

### Diversity & Inclusion
- Headcount by gender × seniority level (board, senior management, total workforce)
- Pay gap by gender: median and mean pay gap (UK Gender Pay Gap reporting format)
- Pay gap by ethnicity (voluntary in most jurisdictions)
- Parental leave: take-up rate by gender, return rate after leave
- Employees with disability (voluntary declaration)

Data source: HR module employee records + payroll data.

### Labour Standards
- Employment type breakdown: permanent / fixed-term / part-time / zero-hours / agency
- Turnover rate (voluntary and involuntary)
- Average tenure by department/level
- Redundancies and restructuring events
- Non-standard contracts as % of total workforce

### Health & Safety
- Recordable injury rate (OSHA TRIR formula: injuries × 200,000 / hours worked)
- Lost Time Injury Frequency Rate (LTIFR)
- Near-miss reports submitted
- Health & safety training hours
- Absenteeism rate (sick days / available days)

### Training & Development
- Average training hours per employee per year
- Training hours by type: compliance / technical / leadership / DEI
- Employees completing mandatory training (% compliance)
- Budget spent on training / total payroll %

---

## Data Entry Modes

1. **Auto-pull from HR**: headcount, turnover, leave data pulled from HR module nightly
2. **Manual entry**: H&S incident counts, training hours (if not tracked in LMS)
3. **LMS integration**: if LMS module active, training hours auto-populated
4. **Survey import**: employee engagement survey scores (CSV import from Qualtrics etc.)

---

## Data Model

### `esg_social_snapshots`
Annual/quarterly snapshot per metric:

| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| period | varchar(7) | "2025-Q4" or "2025" |
| metric_key | varchar(100) | e.g. "gender_pay_gap_mean_pct" |
| value | decimal(12,4) | |
| unit | varchar(50) | "%" / "hours" / "count" |
| breakdown | json | {"gender": "female", "level": "senior"} |
| data_source | enum | hr_auto/manual/lms/survey_import |
| notes | text | nullable |

---

## UK Gender Pay Gap Report

Auto-generated for UK companies with ≥250 employees (mandatory annually):
- Mean and median gender pay gap
- Mean and median bonus gender pay gap
- Proportion of men/women in each pay quartile
- Proportion receiving a bonus

Export: formatted report matching ACAS template. Can be submitted to government portal manually.

---

## Migration

```
932000_create_esg_social_snapshots_table
932001_create_esg_social_targets_table
```

---

## Related

- [[MOC_ESG]]
- [[carbon-footprint-tracking]]
- [[governance-reporting]]
- [[esg-report-builder]] — data flows into ESRS S1 section
- [[MOC_HR]] — primary data source
- [[supply-chain-sustainability]] — ESRS S2
