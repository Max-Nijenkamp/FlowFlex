---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.anomalies
status: planned
color: "#4ADE80"
---

# Anomaly Detection

> AI-powered monitoring of key metrics that alerts you when something unusual happens — before it appears in the next monthly review.

**Panel:** `analytics`
**Module key:** `analytics.anomalies`

## What It Does

Anomaly Detection continuously monitors configured metrics from any FlowFlex module or external connector and raises an alert when a value falls outside the statistically expected range. The system builds a baseline from 90+ days of historical data, learns seasonal patterns (weekends, month-end, Q4 spikes), and flags genuine anomalies rather than routine variation. Alerts route to the responsible owner via email, Slack, or in-app notification with the context needed to start investigating immediately.

## Features

### Core
- Metric monitoring: monitor any FlowFlex metric or external connector metric (revenue, ticket volume, churn, stock levels, ad spend, conversion rate)
- Statistical baseline: system learns normal range, trend, and seasonal pattern from 90 days of history; updates continuously
- Anomaly types: spike, drop, missing data, threshold breach, trend change, and forecast deviation
- Alert severity: critical (immediate action required), warning (investigate soon), info (noteworthy but not urgent)
- Notification channels: in-app notification, email, Slack channel, or SMS
- Assigned owner: each alert rule has a responsible person who receives the notification

### Advanced
- Sensitivity configuration: scale from 1 (few alerts, only large deviations) to 5 (more sensitive, catches smaller changes)
- Alert suppression: do not re-alert on the same rule for a configurable suppression window after firing
- Alert triage: in-app inbox; actions are acknowledge (investigating), mark as expected (e.g., Black Friday spike), escalate to senior stakeholder, or create a task
- Multi-metric rules: alert when metric A drops AND metric B spikes simultaneously (correlated anomaly detection)
- Anomaly history: timeline of all past alerts per metric with resolution notes
- Scheduled review: weekly digest of all anomalies from the past 7 days delivered to data team

### AI-Powered
- Root cause suggestions: when an anomaly fires, the AI queries related metrics to suggest likely explanatory factors
- Noise reduction learning: the system learns from "mark as expected" actions to reduce false positives over time

## Data Model

```erDiagram
    an_alert_rules {
        ulid id PK
        ulid company_id FK
        string name
        string metric_key
        string detection_method
        decimal threshold_value
        integer sensitivity
        string severity
        json notification_channels
        ulid assignee_id FK
        integer suppression_minutes
        boolean is_active
        timestamps timestamps
    }

    an_anomaly_alerts {
        ulid id PK
        ulid rule_id FK
        timestamp detected_at
        decimal metric_value
        decimal expected_value
        decimal deviation_pct
        string status
        ulid acknowledged_by FK
        timestamp resolved_at
        text resolution_notes
    }

    an_alert_rules ||--o{ an_anomaly_alerts : "fires"
```

| Table | Purpose |
|---|---|
| `an_alert_rules` | Alert configuration per monitored metric |
| `an_anomaly_alerts` | Fired alerts with values, status, and resolution |

## Permissions

```
analytics.anomalies.view-any
analytics.anomalies.manage-rules
analytics.anomalies.acknowledge
analytics.anomalies.resolve
analytics.anomalies.delete
```

## Filament

**Resource class:** `AlertRuleResource`
**Pages:** List, Create, Edit
**Custom pages:** `AnomalyInboxPage` (triage view of all open alerts with context), `AlertHistoryPage` (per-rule alert timeline)
**Widgets:** `OpenAlertsWidget` (count of critical and warning alerts currently open)
**Nav group:** Data

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Datadog (business metrics) | Metric anomaly detection and alerting |
| Monte Carlo (data observability) | Metric quality monitoring and alerting |
| Anodot | AI-powered business anomaly detection |
| Outlier.ai | Automated business anomaly investigation |

## Implementation Notes

**Detection mechanism:** The spec says "statistically expected range" — the actual algorithm must be decided before build. Two options:
1. **Z-score:** Compute mean and standard deviation of the metric over the last 90 days. Flag if the current value is more than `sensitivity × 1.5` standard deviations from the mean. Pure PHP/SQL — no external AI needed. Works for metrics with roughly normal distributions.
2. **IQR (Interquartile Range):** Compute Q1, Q3, and IQR. Flag if value < Q1 − 1.5×IQR or > Q3 + 1.5×IQR. More robust to outliers than Z-score. Also pure PHP/SQL.

**Recommended:** Z-score with seasonal adjustment. Store rolling 90-day stats (mean, stddev per day-of-week) to account for weekday patterns. Recompute daily via `UpdateAnomalyBaselineJob`.

**Scheduled monitoring:** `CheckAnomalyRulesJob` runs every 15 minutes for critical rules, every hour for warning rules. It queries the latest metric value for each active rule, compares to the baseline, and fires `AnomalyDetectedNotification` if the threshold is breached. Job respects `suppression_minutes` — tracks last alert time per rule in Redis to avoid re-alerting.

**Notification channels — Slack:** The spec mentions Slack as an alert channel. This requires a Slack incoming webhook URL or Slack Bot Token per company. Store in `company_settings` as `slack_webhook_url`. Send via Laravel's HTTP client: `Http::post($webhookUrl, ['text' => $alertMessage])`. This is outbound HTTP — no Reverb needed.

**Filament:** `AnomalyInboxPage` is a custom `Page` — a triage view showing open alerts as cards with context (expected range, actual value, sparkline of recent metric history). The sparkline is a mini chart.js line chart rendered per card. Standard Filament `ListRecords` cannot render mixed-content cards with embedded charts.

**`AlertHistoryPage`** is a standard Filament `ViewRecord`-style page showing the alert timeline for a single rule. Each timeline entry is a fired `an_anomaly_alerts` record shown in a vertical timeline component (custom Blade partial).

**Root cause suggestions:** Call `app/Services/AI/AnomalyRootCauseService.php` passing the anomalous metric name, the deviation details, and the last 5 values of 3–5 related metrics as context. OpenAI GPT-4o returns a plain-language hypothesis. This is called lazily (on-demand when the user clicks "Suggest root cause" in the triage UI) — not on every alert fire.

## Related

- [[kpi-metrics]] — KPI values monitored by anomaly rules
- [[dashboards]] — anomaly alerts link to the relevant dashboard panel
- [[data-connectors]] — external metrics monitored for anomalies
- [[scheduled-reports]] — weekly anomaly digest delivered via scheduled run
