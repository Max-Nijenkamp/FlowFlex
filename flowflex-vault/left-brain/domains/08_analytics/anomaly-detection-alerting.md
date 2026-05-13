---
type: module
domain: Analytics & Reporting
panel: analytics
phase: 3
status: complete
cssclasses: domain-analytics
migration_range: 504000–504499
last_updated: 2026-05-12
right_brain_log: "[[builder-log-analytics-phase6]]"
---

# Anomaly Detection & Alerting

AI monitors KPIs and alerts you when something unusual happens — before it shows up in the monthly report. Revenue spike, cost anomaly, churn increase, stock depletion.

---

## How Detection Works

Baseline: system learns normal patterns from 90+ days of historical data:
- Seasonal patterns (weekend vs weekday, month-end, Q4 spike)
- Trend trajectory
- Expected variance range

Anomaly: metric falls outside expected range by a statistically significant amount (configurable sigma threshold).

---

## Alert Types

| Alert Type | Example |
|---|---|
| Spike | Revenue 3× normal on Tuesday |
| Drop | Daily signups down 40% vs 7-day average |
| Missing data | No sales logged today (system issue?) |
| Threshold breach | Cash below €50k minimum |
| Trend change | Churn trending up 3 weeks in a row |
| Forecast miss | Q3 forecast going to miss target by >10% |

---

## Monitored Metrics

Any metric from any connected module:
- Revenue, MRR, orders, conversions
- Support ticket volume, response time
- Stock levels, order fulfilment rate
- Payroll costs, expense submission rate
- Website traffic, ad spend efficiency

---

## Alert Configuration

Per alert:
- Metric + data source
- Detection method: statistical / threshold / custom rule
- Sensitivity (1–5: fewer false positives vs more catches)
- Notification channels: email, Slack, in-app, SMS
- Assignee: who gets the alert
- Suppression: don't alert again for 24h if already notified

---

## Alert Triage

Alert inbox for data/ops team:
- Acknowledge: "I'm investigating"
- Mark as expected: "This is the Black Friday spike, not anomalous"
- Escalate: forward to senior stakeholder
- Create task: link alert to a task/action

---

## Data Model

### `an_alert_rules`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| metric_key | varchar(200) | |
| method | enum | statistical/threshold/rule |
| threshold | decimal(14,4) | nullable |
| sensitivity | tinyint | 1–5 |
| channels | json | notification channels |
| assignee_id | ulid | FK |

### `an_alerts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| rule_id | ulid | FK |
| detected_at | timestamp | |
| metric_value | decimal(14,4) | |
| expected_value | decimal(14,4) | |
| deviation_pct | decimal(8,4) | |
| status | enum | open/acknowledged/resolved/expected |

---

## Migration

```
504000_create_an_alert_rules_table
504001_create_an_alerts_table
```

---

## Related

- [[MOC_Analytics]]
- [[dashboard-builder]]
- [[data-connectors-etl]]
- [[scheduled-reports]]
