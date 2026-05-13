---
type: module
domain: AI & Automation
panel: ai
module-key: ai.anomalies
status: planned
color: "#4ADE80"
---

# Anomaly Detection

> Real-time statistical anomaly detection on business metrics across all FlowFlex domains, with configurable sensitivity and alerting.

**Panel:** `ai`
**Module key:** `ai.anomalies`

---

## What It Does

Anomaly Detection monitors configured business metrics in near real-time and alerts when values deviate significantly from historical patterns. Administrators subscribe metrics — such as daily revenue, support ticket volume, or stock movement — to anomaly monitoring, configure sensitivity levels, and define who receives alerts. When an anomaly is detected, the system provides context: the expected range, the actual value, and similar historical events for reference. Detected anomalies can automatically trigger workflows for investigation or remediation.

---

## Features

### Core
- Metric subscription: connect any quantitative metric from FlowFlex panels to anomaly monitoring
- Statistical baseline: system learns normal patterns from historical data automatically
- Sensitivity configuration: adjust how aggressively anomalies are flagged (low/medium/high)
- Real-time alerting: notify configured users via in-app notification and email when an anomaly is detected
- Anomaly detail view: expected range, actual value, deviation magnitude, and detection timestamp
- Dismiss and acknowledge: users can acknowledge false positives to improve future detection

### Advanced
- Multi-metric correlation: detect anomalies that appear simultaneously across related metrics
- Seasonal adjustment: detect anomalies relative to expected seasonal patterns (weekday vs weekend, month-end spikes)
- Anomaly history log: searchable log of all detected anomalies with resolution status
- Custom threshold overrides: override statistical thresholds with manual min/max boundaries for specific metrics
- Anomaly scoring: rank anomalies by severity and business impact estimate

### AI-Powered
- Automatic root cause suggestion: when an anomaly is detected, AI suggests likely causes based on correlated metric changes
- Pattern memory: learn from acknowledged false positives to reduce noise over time
- Predictive pre-anomaly warning: flag metrics trending toward anomaly conditions before they breach

---

## Data Model

```erDiagram
    anomaly_monitors {
        ulid id PK
        ulid company_id FK
        string metric_name
        string source_module
        string aggregation
        string sensitivity
        json alert_recipients
        boolean is_active
        timestamps created_at_updated_at
    }

    anomaly_events {
        ulid id PK
        ulid monitor_id FK
        ulid company_id FK
        decimal expected_value
        decimal actual_value
        decimal deviation_percent
        string severity
        string status
        text root_cause_suggestion
        timestamp detected_at
        timestamp acknowledged_at
        ulid acknowledged_by FK
    }

    anomaly_monitors ||--o{ anomaly_events : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `anomaly_monitors` | Metric monitoring configs | `id`, `company_id`, `metric_name`, `source_module`, `sensitivity`, `is_active` |
| `anomaly_events` | Detected anomalies | `id`, `monitor_id`, `expected_value`, `actual_value`, `deviation_percent`, `severity`, `status` |

---

## Permissions

```
ai.anomalies.view
ai.anomalies.manage-monitors
ai.anomalies.acknowledge
ai.anomalies.configure-alerts
ai.anomalies.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\AnomalyMonitorResource`
- **Pages:** `ListAnomalyMonitors`, `CreateAnomalyMonitor`, `EditAnomalyMonitor`
- **Custom pages:** `AnomalyEventLogPage`, `LiveMonitoringDashboardPage`
- **Widgets:** `ActiveAnomaliesWidget`, `MetricHealthWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Datadog | Custom ML | Splunk |
|---|---|---|---|---|
| Business metric monitoring | Yes | Infrastructure focus | Custom | Log focus |
| Root cause suggestion | Yes | Partial | No | Partial |
| No-code metric subscription | Yes | No | No | No |
| Native platform integration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Relationship to `analytics/anomaly-detection`:** There are two anomaly detection modules in the platform — `analytics.anomalies` and `ai.anomalies`. They are architecturally similar but serve different audiences. `analytics.anomalies` is owned by the Analytics panel and focuses on business metric monitoring with a UI. `ai.anomalies` is the AI panel module with more sophisticated AI-powered features (predictive pre-anomaly warning, automatic root cause). For implementation, these two modules should share a common `app/Services/AI/AnomalyDetectionService.php` service class — do not implement the detection algorithm twice.

**Detection algorithm:** Same as `analytics.anomalies` — Z-score against a rolling 90-day baseline. The `ai.anomalies` module adds: predictive pre-anomaly warning (detect trends toward anomaly using linear regression on the last 14 data points — flag when the projected value in 2 days would breach the threshold), and multi-metric correlation (compute Pearson correlation between monitored metrics — if two metrics are historically correlated but diverge, flag as a correlated anomaly).

**`LiveMonitoringDashboardPage`:** A custom Filament `Page` that auto-refreshes via Livewire polling (`wire:poll.30000ms`) every 30 seconds. It shows a grid of metric health gauges (chart.js doughnut charts) for all active monitors. Colour: green (within baseline), amber (approaching threshold), red (anomaly active). Real-time updates via Reverb broadcast are preferred over polling if Reverb is available — listen on `analytics.{company_id}` channel for `MetricUpdated` events.

**Reverb broadcast for real-time alerts:** When `CheckAnomalyRulesJob` detects an anomaly, it broadcasts an `AnomalyDetected` event on the company's private channel. The `LiveMonitoringDashboardPage` Livewire component listens for this event and updates the affected monitor's gauge in real time without a full page refresh. Also broadcasts to the `user.{assigned_user_id}` channel to update the `ActiveAnomaliesWidget` badge count.

**Root cause suggestion:** Calls `app/Services/AI/AnomalyRootCauseService.php` passing: the anomalous metric name, the current value, the expected value, and the last 7-day time series for 5 correlated metrics. OpenAI GPT-4o is prompted to reason about likely causality. This call is made on-demand (user clicks "Suggest root cause" button in the triage UI) — not automatically on detection, to avoid token costs on every alert.

## Related

- [[predictive-analytics]] — anomaly signals feed prediction models
- [[workflow-builder]] — anomalies can trigger automated workflows
- [[fpa/INDEX]] — financial metric anomaly monitoring
- [[analytics/INDEX]] — cross-platform analytics data source
