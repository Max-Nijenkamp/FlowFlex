---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: planned
cssclasses: domain-fpa
migration_range: 987500–987999
last_updated: 2026-05-09
---

# Board Reporting Pack

Auto-generate the monthly or quarterly management/board pack. Pulls live financials, KPIs, and commentary into a polished PDF or slide deck. Eliminates 2 days of manual assembly.

---

## What Goes in the Pack

### Financial Statements
- P&L: actual vs budget vs prior year, with YTD
- Balance Sheet: current month-end
- Cash Flow Statement: operating / investing / financing
- Revenue waterfall: breakdown by segment/product

### KPI Scorecard
Finance team configures KPIs pulled from across FlowFlex modules:
- Gross margin %, EBITDA, cash runway
- MRR / ARR (from Subscription Billing)
- NRR, churn rate (from Customer Success)
- Headcount (from HR)
- Open pipeline value (from CRM)
- Project utilisation % (from PSA)

### Operational Highlights
- Text sections for each department head to input narrative
- Pre-populated with AI-drafted commentary based on data (editable)

---

## Pack Templates

Configurable report templates:
- **Monthly management accounts**: 4–6 page CFO pack
- **Quarterly board pack**: 12–20 page full board format
- **Investor update**: MRR/ARR focused, growth metrics
- **Lender covenant pack**: specific financial ratio covenants

---

## Build Process

1. Finance triggers pack build (or auto-schedule)
2. System pulls data from all integrated modules
3. Charts and tables auto-rendered
4. Narrative sections open for input (deadline tracked)
5. Finance reviews → approves → distributes

Distribution: secure link (board members view online, no PDF emailed) or PDF export.

---

## Versioning

Every published pack versioned and archived. Board members can compare current vs prior period packs. Audit trail of who viewed the pack.

---

## Data Model

### `fpa_report_packs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| template_id | ulid | FK |
| period | date | month/quarter |
| status | enum | building/review/published |
| published_at | timestamp | nullable |

### `fpa_pack_sections`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| pack_id | ulid | FK |
| section_type | enum | financial/kpi/narrative/chart |
| title | varchar(200) | |
| content | json | rendered data or markdown |
| sort_order | int | |

---

## Migration

```
987500_create_fpa_report_packs_table
987501_create_fpa_pack_sections_table
987502_create_fpa_pack_templates_table
```

---

## Related

- [[MOC_FPA]]
- [[budget-vs-actual-reporting]]
- [[rolling-forecasts]]
- [[MOC_SubscriptionBilling]] — MRR/ARR
- [[MOC_CustomerSuccess]] — NRR/churn
- [[MOC_Finance]] — financial statements
