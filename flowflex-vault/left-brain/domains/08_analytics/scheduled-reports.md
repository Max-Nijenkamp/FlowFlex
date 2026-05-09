---
type: module
domain: Analytics & BI
panel: analytics
cssclasses: domain-analytics
phase: 6
status: planned
migration_range: 450000–499999
last_updated: 2026-05-09
---

# Scheduled Reports

Auto-deliver reports (PDF/Excel/CSV) to configured recipients on a schedule. Every manager expects to find their KPI report in their inbox Monday morning without opening the app. Standard analytics feature, often blocking for enterprise adoption.

**Panel:** `analytics`  
**Phase:** 6

---

## Features

### Schedule Configuration
- Any saved report or dashboard → schedule it
- Frequencies: hourly, daily, weekly, bi-weekly, monthly, quarterly
- Day and time selector (e.g. every Monday at 07:00)
- Timezone per schedule
- Conditional send: "only send if there is data to report" (skip empty reports)
- Pause/resume schedule without deleting

### Recipients
- Internal users (FlowFlex accounts)
- External email addresses (e.g. board members, clients, investors — no FlowFlex login needed)
- Dynamic recipient lists (e.g. all users with "Finance Manager" role)
- CC/BCC support

### Delivery Formats
- PDF (formatted, print-ready)
- Excel (.xlsx) with data in table, charts as images
- CSV (raw data only, for further analysis)
- Inline email (for small tables — displayed in email body without attachment)

### Report Content Options
- Snapshot of dashboard (screenshot with live data at delivery time)
- Data table export (raw query results)
- KPI summary card (key metrics at a glance)
- Comparison to previous period auto-included
- Custom email subject and body text

### Delivery Channels
- Email (primary)
- Slack (send to channel or DM)
- MS Teams (webhook)
- Webhook (POST JSON payload to external URL)
- SFTP (for legacy integrations)

### Delivery Log
- Every scheduled send logged: timestamp, recipients, status (delivered/failed)
- Re-send failed delivery manually
- Preview last-sent report

---

## Data Model

```erDiagram
    report_schedules {
        ulid id PK
        ulid company_id FK
        ulid report_id FK
        ulid created_by FK
        string frequency
        string cron_expression
        string timezone
        json recipients
        string delivery_format
        string delivery_channel
        json channel_config
        boolean is_active
        boolean skip_empty
        timestamp last_sent_at
        timestamp next_send_at
    }

    report_delivery_log {
        ulid id PK
        ulid schedule_id FK
        string status
        json recipients_sent_to
        string error_message
        timestamp sent_at
    }
```

---

## Permissions

```
analytics.scheduled-reports.create
analytics.scheduled-reports.manage-team
analytics.scheduled-reports.add-external-recipients
```

---

## Related

- [[MOC_Analytics]]
- [[MOC_CorePlatform]] — notification delivery infrastructure
