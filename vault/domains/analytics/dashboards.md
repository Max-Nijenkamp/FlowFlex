---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.dashboards
status: planned
color: "#4ADE80"
---

# Dashboards

> Build configurable dashboards with widgets sourced from any FlowFlex domain, arranged freely with drag-and-drop, and shared with teams or external stakeholders.

**Panel:** `analytics`
**Module key:** `analytics.dashboards`

## What It Does

Dashboards is the visual analytics layer for the entire FlowFlex platform. Any user can create a dashboard by dragging widgets onto a grid and configuring each widget's data source, filters, time period, and chart type. Widgets pull live data from connected FlowFlex modules — CRM pipeline from sales, MRR from subscriptions, headcount from HR, stock levels from operations — so a single dashboard can show a cross-functional view of the business in real time. Dashboards can be kept private, shared to a team, embedded in another module, or published to external stakeholders via a secure read-only link.

## Features

### Core
- Drag-and-drop grid layout: place and resize widgets freely; grid snaps to columns
- Widget types: KPI card, line chart, bar chart, pie/donut chart, data table, funnel, gauge, text/markdown, map
- Data sources: any FlowFlex module (CRM, Finance, HR, Operations, Subscriptions, E-commerce, Marketing)
- Per-widget configuration: data source, grouping dimension, aggregation (sum, count, average), date range, filters
- Dashboard-level date filter: single date picker applies to all widgets simultaneously
- Access levels: private, team/role-based, organisation-wide read, external secure link

### Advanced
- Multiple dashboards per user: unlimited dashboards; organise with folders and tags
- Embed in module: pin a dashboard as the homepage of another panel (e.g., executive homepage in the Admin panel)
- Cross-domain widgets: place CRM pipeline and Finance AR aging side-by-side on one dashboard
- Click-through drill-down: click a bar segment or data point to filter a linked table widget below
- Dashboard subscriptions: other users subscribe to receive a scheduled snapshot via email
- Dark mode and branded themes: apply company colours and logo to dashboards shared externally

### AI-Powered
- Dashboard template suggestions: recommend starter dashboard layouts for common roles (CEO, CFO, Sales Manager, HR Manager)
- Auto-insight annotations: highlight statistically notable changes on charts with an AI-generated sentence

## Data Model

```erDiagram
    an_dashboards {
        ulid id PK
        ulid company_id FK
        string name
        ulid created_by FK
        string visibility
        string folder
        json tags
        json layout_config
        timestamps timestamps
        softDeletes deleted_at
    }

    an_widgets {
        ulid id PK
        ulid dashboard_id FK
        string type
        string title
        string data_source_module
        string data_source_key
        json query_config
        json display_config
        json position
        timestamps timestamps
    }

    an_dashboard_shares {
        ulid id PK
        ulid dashboard_id FK
        string share_type
        ulid team_id FK
        string external_token
        timestamp expires_at
        timestamps timestamps
    }

    an_dashboards ||--o{ an_widgets : "contains"
    an_dashboards ||--o{ an_dashboard_shares : "shared via"
```

| Table | Purpose |
|---|---|
| `an_dashboards` | Dashboard configuration and access settings |
| `an_widgets` | Individual widget definitions and query config |
| `an_dashboard_shares` | Team shares and external secure links |

## Permissions

```
analytics.dashboards.view-any
analytics.dashboards.create
analytics.dashboards.update
analytics.dashboards.share
analytics.dashboards.delete
```

## Filament

**Resource class:** `DashboardResource`
**Pages:** List, Create, Edit
**Custom pages:** `DashboardViewPage` (live rendered dashboard with drag-and-drop edit mode)
**Widgets:** none — the module itself is the widget system
**Nav group:** Dashboards

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Tableau | Interactive data visualisation and dashboards |
| Looker | Business intelligence dashboards |
| Power BI | Self-service BI and dashboard sharing |
| Metabase | Open-source BI for internal teams |

## Implementation Notes

**Filament:** `DashboardViewPage` is the highest-complexity custom `Page` in the entire platform. It must render a configurable grid of heterogeneous chart widgets with drag-and-drop layout editing. This is not achievable with standard Filament Widgets (which have fixed positions). Two implementation paths:

1. **Livewire-first approach:** The dashboard grid is a Livewire component. Each widget slot renders a Livewire component determined by `an_widgets.type`. Layout is stored in `an_dashboards.layout_config` as a JSON grid definition (x, y, w, h per widget). Drag-and-drop grid editing uses `gridstack.js` (MIT) — the JS library manages the visual grid; drop events post back to a Livewire `updateLayout()` action. Chart rendering uses `chart.js` with data loaded via Livewire `getChartData()` per widget.

2. **Vue component approach:** The dashboard view page embeds a Vue 3 component via Vite (the Inertia approach is not used here — it's loaded as a Filament custom view). This gives full Vue reactivity and better chart library support but adds architectural complexity for a Filament page.

**Recommended:** Livewire-first with gridstack.js + chart.js. Establish the pattern for the first two or three widget types, then extend.

**Cross-domain data fetching:** Each widget's `data_source_module` and `data_source_key` keys must resolve to a registered data provider. Define `app/Contracts/Analytics/WidgetDataProviderInterface.php` with a `getData(array $queryConfig): array` method. Each domain registers its providers in its ServiceProvider: `WidgetDataRegistry::register('crm.pipeline_value', CrmPipelineValueProvider::class)`. This registry pattern allows the analytics module to remain decoupled from all domain modules.

**Real-time:** Reverb broadcasting is needed for "Live dashboard metrics" — dashboards with widgets configured as real-time (refresh-on-event rather than scheduled). Broadcast `MetricUpdated` events from domain modules (e.g. `DealWon`, `InvoicePaid`) on a company-level channel `analytics.{company_id}`. Widgets subscribed to real-time mode listen via Livewire's `$listeners` for a `metricUpdated` event and re-fetch their data.

**Scheduled snapshot emails:** `DashboardSubscriptionJob` runs the configured schedule (daily/weekly) — renders each widget's data server-side, generates a PNG chart image via `spatie/browsershot`, embeds images in an HTML email, and sends via Laravel Mail. This requires Node.js + Puppeteer in the Docker image.

**External secure link:** `an_dashboard_shares.external_token` is a signed token. The public view route renders the dashboard in read-only mode with all widgets using the company's data, authenticated via the token only (no FlowFlex user session needed). The route must enforce the `expires_at` from the share record.

**AI features:** Dashboard template suggestions and auto-insight annotations both call `app/Services/AI/DashboardInsightService.php`. The auto-insight runs as a daily scheduled job per active dashboard — it serialises widget data to JSON, sends to OpenAI GPT-4o, and stores the returned annotations in a `an_dashboard_annotations {ulid id, ulid widget_id, text annotation, date annotation_date}` table (not currently defined — add it).

## Related

- [[kpi-metrics]] — KPI cards on dashboards pull from KPI definitions
- [[scheduled-reports]] — dashboards can be scheduled for delivery
- [[data-connectors]] — external data sources appear as widget options
- [[anomaly-detection]] — anomaly alerts link to the relevant dashboard
