---
type: module
domain: Subscription Billing & RevOps
panel: billing
module-key: billing.mrr
status: planned
color: "#4ADE80"
---

# MRR Analytics

> Read-only MRR dashboard — new MRR, expansion, contraction, churn, net revenue retention, and cohort analysis.

**Panel:** `billing`
**Module key:** `billing.mrr`

---

## What It Does

MRR Analytics provides the RevOps intelligence layer for the billing panel. It tracks Monthly Recurring Revenue movements — new business, expansion from upgrades, contraction from downgrades, churned revenue, and reactivations — and presents them in the standard SaaS revenue waterfall format. Net Revenue Retention (NRR) is calculated per cohort and in aggregate. All metrics are computed from subscription and invoice data with no manual entry required, giving finance and RevOps leaders a real-time view of revenue health.

---

## Features

### Core
- MRR waterfall: new MRR, expansion MRR, contraction MRR, churn MRR, reactivation MRR, and net new MRR per month
- Total MRR trend: rolling 12-month MRR trend with month-on-month change
- Gross Revenue Retention (GRR): percentage of MRR retained excluding expansions
- Net Revenue Retention (NRR): percentage of MRR retained including expansions; benchmark indicator
- ARR: annualised recurring revenue (MRR × 12) displayed prominently
- Account-level MRR: drill down to see each account's MRR contribution and movement history

### Advanced
- Cohort NRR: NRR broken down by the cohort of accounts that signed up in the same quarter
- Plan-level MRR: MRR breakdown by subscription plan to identify revenue concentration
- Geographic MRR: revenue breakdown by customer country where applicable
- Segment MRR: enterprise vs SMB MRR split with separate NRR calculations
- MRR forecasting: project next-quarter MRR based on current growth rate and churn rate

### AI-Powered
- Growth anomaly detection: flag months where MRR growth rate deviates significantly from trend
- Churn MRR concentration: identify if churn is concentrated among a particular plan or cohort
- NRR benchmark comparison: compare NRR against publicly available SaaS benchmarks for the company's ARR band

---

## Data Model

```erDiagram
    mrr_monthly_snapshots {
        ulid id PK
        ulid company_id FK
        date month
        decimal new_mrr
        decimal expansion_mrr
        decimal contraction_mrr
        decimal churn_mrr
        decimal reactivation_mrr
        decimal total_mrr
        decimal arr
        decimal nrr
        decimal grr
        timestamps created_at_updated_at
    }

    account_mrr_history {
        ulid id PK
        ulid account_id FK
        ulid company_id FK
        date month
        decimal mrr
        string movement_type
        decimal movement_amount
    }

    mrr_monthly_snapshots }o--|| companies : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `mrr_monthly_snapshots` | Monthly MRR aggregates | `id`, `company_id`, `month`, `new_mrr`, `churn_mrr`, `total_mrr`, `nrr`, `grr` |
| `account_mrr_history` | Per-account MRR records | `id`, `account_id`, `month`, `mrr`, `movement_type`, `movement_amount` |

---

## Permissions

```
billing.mrr.view
billing.mrr.view-account-detail
billing.mrr.view-cohort-data
billing.mrr.export
billing.mrr.view-forecast
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `MrrDashboardPage`, `NrrCohortPage`, `PlanMrrPage`, `MrrForecastPage`
- **Widgets:** `TotalMrrWidget`, `NrrWidget`, `ChurnMrrWidget`, `MrrWaterfallWidget`
- **Nav group:** Revenue

---

## Displaces

| Feature | FlowFlex | Chargebee | Baremetrics | ChartMogul |
|---|---|---|---|---|
| MRR waterfall | Yes | Yes | Yes | Yes |
| NRR cohort analysis | Yes | Yes | Yes | Yes |
| AI growth anomaly detection | Yes | No | No | No |
| Native billing data | Yes | Yes | Sync-based | Sync-based |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Filament:** `MrrDashboardPage`, `NrrCohortPage`, `PlanMrrPage`, and `MrrForecastPage` are all custom `Page` classes — read-only analytics views with charts. None use standard Filament resources. Chart rendering uses **chart.js** (already in the stack) via Livewire components. The MRR waterfall is a chart.js bar chart with stacked positive/negative segments. The cohort NRR table is an HTML `<table>` rendered in Blade with conditional colour coding (red < 100%, green > 110%).

**MRR computation:** `mrr_monthly_snapshots` is populated by a monthly job `ComputeMrrSnapshotJob` run on the 1st of each month for the prior month. The computation requires:
1. Query all active subscriptions at month start and month end.
2. Classify changes: new subscription → new MRR. Upgrade → expansion MRR. Downgrade → contraction MRR. Cancellation → churn MRR. Reactivation → reactivation MRR.
3. This requires `billing_subscriptions` to have `started_at` and `ended_at` timestamp columns and `company_module_subscriptions.price_per_seat × seat_count` for MRR value per subscription — the `billing-engine` module's data model must support this.

**`account_mrr_history.movement_type`** should be an enum: `new | expansion | contraction | churn | reactivation`. Add a unique constraint on `(account_id, company_id, month)`.

**The `billing.mrr` module is FlowFlex's own internal SaaS metrics** — it tracks MRR that FlowFlex earns from its tenant companies. This is distinct from the `subscription-billing` domain modules which track MRR that a FlowFlex tenant company earns from their own customers. Make this distinction clear in the panel separation: `billing.mrr` is in the FlowFlex admin panel (`/admin`) and `subscription-billing` modules are in the tenant's billing panel.

**AI features:** Growth anomaly detection uses the same Z-score approach as `analytics.anomalies` — apply it to the `total_mrr` time series. No LLM needed. Churn concentration analysis is a SQL aggregate (group `account_mrr_history` by plan or cohort, find the segment with the highest churn as a % of its starting MRR). NRR benchmark comparison calls `app/Services/AI/MrrBenchmarkService.php` which either uses a hardcoded benchmark table (e.g. public SaaS benchmarks from KeyBanc surveys) or calls OpenAI GPT-4o with the company's ARR and sector to return a benchmark range.

## Related

- [[invoicing]] — invoice payments drive MRR calculations
- [[subscription-plans]] — plan movements create MRR expansion/contraction events
- [[dunning]] — recovered payments affect churn and reactivation MRR
- [[revenue-recognition]] — recognised revenue separate from MRR cash basis
