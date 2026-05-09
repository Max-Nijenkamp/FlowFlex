---
type: module
domain: Analytics & Reporting
panel: analytics
phase: 3
status: planned
cssclasses: domain-analytics
migration_range: 503500–503999
last_updated: 2026-05-09
---

# Data Connectors & ETL

Connect external data sources into FlowFlex analytics. Pull data from third-party SaaS tools, databases, and files. Normalise, transform, and make available for dashboards and reports.

---

## Pre-Built Connectors

| Source | Data pulled |
|---|---|
| Stripe / Mollie | Payment transactions, MRR, churn |
| HubSpot / Salesforce | Contacts, deals, pipeline |
| Google Analytics 4 | Web sessions, conversions |
| Google Ads / Meta Ads | Spend, impressions, clicks, ROAS |
| Shopify / WooCommerce | Orders, products, customers |
| Xero / QuickBooks | Invoices, expenses, P&L |
| Postgres / MySQL | Custom SQL query |
| REST API | Generic webhook/API connector |
| CSV/Excel upload | Manual data import |

---

## ETL Pipeline

Each connector runs as a scheduled ETL:
1. **Extract**: pull data from source API/DB since last sync
2. **Transform**: map source fields to FlowFlex schema, clean/normalise
3. **Load**: write to analytics data store

Frequency: real-time (webhook), hourly, daily. Configurable per connector.

---

## Field Mapping

When source field names differ from FlowFlex schema:
- Visual mapper: drag source fields to destination fields
- Data type conversion (string date → datetime, currency string → decimal)
- Computed fields: `revenue_net = revenue_gross × (1 − tax_rate)`

---

## Data Catalogue

Each connected source has a catalogue entry:
- Available tables/fields + descriptions
- Last sync timestamp + row count
- Data freshness indicator

Used by dashboard builder when selecting data sources.

---

## Error Handling

Sync failures:
- Alert to data admin on failure
- Partial sync: rows that failed marked for retry
- Schema change detected: alert + pause sync until mapping reviewed

---

## Data Model

### `an_connectors`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| type | varchar(100) | "stripe", "postgres", etc |
| config | json | encrypted credentials + settings |
| sync_frequency | enum | realtime/hourly/daily |
| last_synced_at | timestamp | nullable |
| status | enum | active/paused/error |

### `an_sync_runs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| connector_id | ulid | FK |
| started_at | timestamp | |
| completed_at | timestamp | nullable |
| rows_synced | int | |
| rows_failed | int | |
| error_message | text | nullable |

---

## Migration

```
503500_create_an_connectors_table
503501_create_an_sync_runs_table
503502_create_an_field_mappings_table
```

---

## Related

- [[MOC_Analytics]]
- [[dashboard-builder]]
- [[scheduled-reports]]
- [[anomaly-detection-alerting]]
