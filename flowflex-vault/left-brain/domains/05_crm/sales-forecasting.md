---
type: module
domain: CRM & Sales
panel: crm
phase: 3
status: planned
cssclasses: domain-crm
migration_range: 307000–307499
last_updated: 2026-05-09
---

# Sales Forecasting

AI-assisted pipeline forecast. Combines rep-submitted commits with ML probability scoring to give leadership a realistic view of revenue this quarter and next.

---

## Forecast Mechanics

Two signals combined:

**Rep commit**: each rep categorises their deals:
- **Commit**: high confidence, will close this period
- **Best case**: likely but not certain
- **Pipeline**: possible but uncertain
- **Omit**: not expected this period

**ML probability**: system scores each deal based on:
- Stage × historical win rates at that stage
- Deal age (older deals decay)
- Engagement signals (emails opened, meetings, docs viewed)
- Similar deal patterns (company size, industry, rep)

**Forecast = Committed + (Best Case × ML confidence) + (Pipeline × ML confidence)**

---

## Forecast Views

- **Aggregate**: total company, by region, by team
- **Rep rollup**: each rep's commit vs ML forecast
- **Week-over-week change**: is pipeline growing or shrinking?
- **Gap to target**: forecast vs quota for the period

---

## Deal Risk Signals

AI-flagged deal risks:
- No activity in 14+ days on a commit deal
- Decision deadline passed without close
- Champion left company
- Competitor mentioned in email thread
- Deal value dropped since creation

---

## Historical Accuracy

Track forecast accuracy over time:
- Final actual revenue vs forecast made 4 weeks out, 8 weeks out
- By rep: who is most accurate? Who over-commits?
- Drives coaching conversations

---

## Data Model

### `crm_forecast_periods`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| period_type | enum | weekly/monthly/quarterly |
| period_start | date | |
| period_end | date | |
| target_revenue | decimal(14,2) | |

### `crm_forecast_submissions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| period_id | ulid | FK |
| rep_id | ulid | FK |
| opportunity_id | ulid | FK |
| category | enum | commit/best_case/pipeline/omit |
| amount | decimal(14,2) | |
| ml_probability | decimal(5,4) | |
| submitted_at | timestamp | |

---

## Migration

```
307000_create_crm_forecast_periods_table
307001_create_crm_forecast_submissions_table
```

---

## Related

- [[MOC_CRM]]
- [[territory-quota-management]]
- [[MOC_FPA]] — revenue feed into financial forecast
