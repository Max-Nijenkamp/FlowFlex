---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.scheduled
status: planned
color: "#4ADE80"
---

# Scheduled Reports

> Automatically deliver saved report extractions and dashboard snapshots to stakeholders by email or Slack on a defined cadence.

**Panel:** `analytics`
**Module key:** `analytics.scheduled`

## What It Does

Scheduled Reports automates the delivery of data outputs to people who need information regularly but should not have to remember to log in and run it manually. A schedule links a saved report configuration or a dashboard to a delivery cadence (daily, weekly, monthly) and a list of recipients. At the defined time the system generates the output, attaches it to an email or posts it to a Slack channel, and logs the delivery. Recipients receive data without needing a FlowFlex account.

## Features

### Core
- Schedule creation: select a saved report configuration or a dashboard, set cadence (daily, weekly, monthly, quarterly), and define first delivery date
- Recipient list: FlowFlex users or external email addresses; no FlowFlex account required for external recipients
- Delivery channels: email with attached PDF or CSV; Slack channel post with chart image preview; Microsoft Teams webhook
- Output format: PDF (formatted, branded) or CSV (raw data) for report outputs; PNG snapshot for dashboard deliveries
- Schedule status: active, paused, or completed; enable/disable without deleting the schedule
- Delivery log: timestamp and status (delivered, failed) for every past delivery attempt

### Advanced
- Conditional delivery: only send if a condition is met (e.g., "only send the low-stock report if there are SKUs below reorder point")
- Dynamic recipients: recipient list driven by a role or team — automatically updates when team membership changes
- Custom email subject and body: personalise the covering email message with a template
- On-demand run: trigger a scheduled report immediately outside the cadence for an ad-hoc delivery
- Failure retry: retry failed deliveries automatically up to 3 times; alert the schedule owner if all retries fail
- Timezone-aware delivery: delivery time interpreted in the recipient's local timezone

### AI-Powered
- Suggested cadence: recommend a delivery frequency based on how often the underlying data changes
- Summary generation: prepend an AI-written plain-language summary to the email body describing key changes since the last delivery

## Data Model

```erDiagram
    an_schedules {
        ulid id PK
        ulid company_id FK
        string name
        string source_type
        ulid source_id FK
        string cadence
        string delivery_day
        time delivery_time
        string timezone
        json recipient_emails
        json slack_channels
        string output_format
        boolean is_active
        timestamp next_run_at
        timestamps timestamps
    }

    an_schedule_deliveries {
        ulid id PK
        ulid schedule_id FK
        string status
        string file_url
        text error_message
        timestamp delivered_at
    }

    an_schedules ||--o{ an_schedule_deliveries : "logs"
```

| Table | Purpose |
|---|---|
| `an_schedules` | Schedule configuration with cadence and recipients |
| `an_schedule_deliveries` | Delivery log per execution attempt |

## Permissions

```
analytics.scheduled.view-any
analytics.scheduled.create
analytics.scheduled.update
analytics.scheduled.run-now
analytics.scheduled.delete
```

## Filament

**Resource class:** `ScheduleResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `DeliveryLogPage` (history of deliveries for a given schedule)
**Widgets:** `UpcomingDeliveriesWidget` (next 5 scheduled deliveries across all active schedules)
**Nav group:** Reports

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Tableau Subscriptions | Scheduled dashboard delivery by email |
| Power BI Email Subscriptions | Scheduled report delivery to stakeholders |
| Looker Scheduled Looks | Automated data delivery to recipients |
| Google Data Studio (Scheduled email) | Dashboard snapshot delivery |

## Related

- [[data-exports]] — saved report configurations are the source for schedules
- [[dashboards]] — dashboard snapshots delivered on a schedule
- [[kpi-metrics]] — KPI summary digests delivered via schedules
- [[anomaly-detection]] — weekly anomaly digest is a scheduled delivery
