---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.connectors
status: planned
color: "#4ADE80"
---

# Data Connectors

> Connect external data sources — Google Analytics, Stripe, HubSpot, and custom APIs — so external metrics appear alongside FlowFlex data in dashboards and KPI cards.

**Panel:** `analytics`
**Module key:** `analytics.connectors`

## What It Does

Data Connectors lets teams pull metrics from tools outside FlowFlex into the analytics layer without writing code. Each connector authenticates to an external system via OAuth or API key, maps specific metrics to FlowFlex KPI and dashboard data sources, and syncs on a configurable schedule. Once connected, external metrics (Google Analytics sessions, Stripe MRR, HubSpot contacts) appear alongside native FlowFlex data in dashboards, KPI cards, and the custom report builder.

## Features

### Core
- Pre-built connectors: Google Analytics 4, Google Search Console, Stripe, HubSpot, Salesforce, Mailchimp, QuickBooks, Xero, Shopify, and custom REST API
- Authentication: OAuth 2.0 for supported connectors; API key or Bearer token for custom endpoints
- Metric mapping: select which external metrics to import and map to FlowFlex metric keys
- Sync schedule: configure refresh frequency (hourly, daily, or weekly) per connector
- Sync status monitoring: last successful sync timestamp, next scheduled sync, error log for failed syncs
- Metric preview: see the last 7 days of raw values for any mapped metric after connection

### Advanced
- Custom API connector: define base URL, authentication, endpoint path, response JSON path, and pagination for any REST API
- Webhook ingestion: receive push events from external systems and store as time-series metric values
- Data transformation: apply simple formulas to raw values (divide by 1000, multiply by exchange rate, percentage of total)
- Historical backfill: on first connection, pull up to 12 months of historical data for trend comparison
- Connector duplication: clone an existing connector configuration to connect a second account of the same platform
- Field-level mapping review: inspect the exact values being ingested per metric key before publishing to dashboards

### AI-Powered
- Connector health monitor: alert when sync has not returned new data for longer than expected based on the platform's typical data lag
- Metric suggestion: after connection, recommend which external metrics are most commonly used in dashboards by similar companies

## Data Model

```erDiagram
    an_connectors {
        ulid id PK
        ulid company_id FK
        string name
        string platform
        json auth_config
        string status
        json metric_mappings
        string sync_frequency
        timestamp last_synced_at
        timestamp next_sync_at
        timestamps timestamps
    }

    an_connector_metrics {
        ulid id PK
        ulid connector_id FK
        string external_key
        string internal_key
        string unit
        string transformation_formula
        boolean is_active
        timestamps timestamps
    }

    an_external_metric_values {
        ulid id PK
        ulid connector_metric_id FK
        decimal value
        date period_date
        timestamps timestamps
    }

    an_connectors ||--o{ an_connector_metrics : "maps"
    an_connector_metrics ||--o{ an_external_metric_values : "stores"
```

| Table | Purpose |
|---|---|
| `an_connectors` | Connector configuration and sync status |
| `an_connector_metrics` | Metric-level mapping from external to internal key |
| `an_external_metric_values` | Time-series values per mapped metric |

## Permissions

```
analytics.connectors.view-any
analytics.connectors.create
analytics.connectors.update
analytics.connectors.sync
analytics.connectors.delete
```

## Filament

**Resource class:** `ConnectorResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ConnectorSetupWizardPage` (step-by-step auth and metric mapping), `SyncLogPage` (per-connector sync history)
**Widgets:** `ConnectorHealthWidget` (connectors with recent errors)
**Nav group:** Data

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Fivetran (lite) | No-code data connectors to cloud sources |
| Zapier (analytics use) | Pushing external metrics into a central store |
| Supermetrics | Pulling ad and analytics platform data |
| Segment | Event ingestion and metric aggregation |

## Related

- [[dashboards]] — connected external metrics usable in widgets
- [[kpi-metrics]] — external metric values linked to KPI actuals
- [[anomaly-detection]] — external metrics monitored for anomalies
- [[scheduled-reports]] — external data included in scheduled extractions
