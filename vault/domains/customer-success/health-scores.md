---
type: module
domain: Customer Success
panel: cs
module-key: cs.health
status: planned
color: "#4ADE80"
---

# Health Scores

> Customer health scoring with configurable metrics, weightings, and automatic red/amber/green status per account.

**Panel:** `cs`
**Module key:** `cs.health`

---

## What It Does

Health Scores provides a quantitative framework for assessing the health of every customer relationship. CS admins define a set of health metrics — product usage frequency, support ticket volume, NPS score, payment status, onboarding completion, engagement with the CSM — and assign a weight to each. The system calculates a composite health score for every account on a regular basis, assigns a RAG status, and highlights accounts whose scores have fallen significantly. CSMs use the score to prioritise which accounts need attention.

---

## Features

### Core
- Health metric library: define metrics with a data source (PLG usage, billing status, NPS, manual input) and weight
- Composite score calculation: weighted average of all active metrics, normalised to a 0–100 scale
- RAG thresholds: configurable red/amber/green thresholds (e.g. green >70, amber 40–70, red <40)
- Account health dashboard: all accounts ranked by health score with RAG indicator
- Score trend: 90-day trend line for each account's health score
- Score change alerts: notify the CSM when an account's score drops by more than a configurable threshold

### Advanced
- Segment-specific metric sets: different metric weightings for enterprise vs SMB accounts
- Manual override: CSM can override the calculated score with a manual rating and reason
- Score history: full history of score changes for audit and trend analysis
- Benchmark comparison: compare an account's score against the cohort average for its segment
- Multi-product health: calculate separate scores per product line and a composite across all

### AI-Powered
- Metric importance analysis: AI identifies which metrics are most predictive of churn or expansion for each segment
- Score prediction: predict next month's health score based on current trends
- Anomaly detection: flag accounts where individual metric scores are unusually inconsistent

---

## Data Model

```erDiagram
    health_metric_definitions {
        ulid id PK
        ulid company_id FK
        string name
        string data_source
        decimal weight
        boolean is_active
        timestamps created_at_updated_at
    }

    customer_health_scores {
        ulid id PK
        ulid account_id FK
        ulid company_id FK
        decimal composite_score
        string rag_status
        json metric_scores
        boolean is_manual_override
        decimal manual_score
        timestamp calculated_at
    }

    health_metric_definitions ||--o{ customer_health_scores : "feeds"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `health_metric_definitions` | Metric configurations | `id`, `company_id`, `name`, `data_source`, `weight`, `is_active` |
| `customer_health_scores` | Calculated scores | `id`, `account_id`, `composite_score`, `rag_status`, `metric_scores`, `calculated_at` |

---

## Permissions

```
cs.health.view
cs.health.manage-metrics
cs.health.manual-override
cs.health.view-all-accounts
cs.health.export
```

---

## Filament

- **Resource:** `App\Filament\Cs\Resources\CustomerHealthScoreResource`
- **Pages:** `ListCustomerHealthScores`, `ViewCustomerHealthScore`
- **Custom pages:** `HealthDashboardPage`, `MetricConfigPage`
- **Widgets:** `RedAccountsWidget`, `ScoreDistributionWidget`, `TrendingDownWidget`
- **Nav group:** Accounts

---

## Displaces

| Feature | FlowFlex | Gainsight | ChurnZero | Totango |
|---|---|---|---|---|
| Configurable metric weights | Yes | Yes | Yes | Yes |
| RAG status | Yes | Yes | Yes | Yes |
| Score trend history | Yes | Yes | Yes | Yes |
| AI metric importance analysis | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[churn-risk]] — low health scores drive churn risk flags
- [[playbooks]] — health score drops can trigger a playbook
- [[success-plans]] — health score surfaced alongside success plan
- [[onboarding-tracking]] — onboarding completion is a health metric
