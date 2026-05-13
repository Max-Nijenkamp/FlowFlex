---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.product
status: planned
color: "#4ADE80"
---

# Product Analytics

> Event tracking, funnel analysis, retention cohorts, session heatmaps, and feature adoption — understand how users engage with any product without Mixpanel or Amplitude.

**Panel:** `/analytics`
**Module key:** `analytics.product`

## What It Does

Product Analytics captures how users interact with a product — tracking page views, feature clicks, and key events — and turns that raw event stream into actionable insight through funnels, retention cohorts, and feature adoption heatmaps. The module serves two audiences: FlowFlex staff using the Admin panel to understand platform-wide usage across all tenants, and companies that want to embed the FlowFlex tracking SDK into their own product to understand their own users' behaviour. V1 scope is FlowFlex internal usage only — the SDK for company products is V2.

## Features

### Core
- Event tracking: capture named events (e.g. `page_viewed`, `feature_clicked`, `form_submitted`, `module_activated`) with arbitrary `properties` JSON — events are associated with a `company_id` (tenant), `user_id`, and `session_id`
- Page view tracking: automatic page view events captured on every Filament panel navigation — no manual instrumentation required for Filament pages; custom events require one-line JS call for Vue 3 frontend pages
- Session tracking: group events into sessions (30-minute inactivity timeout) with `session_id` — allows per-session analysis (events per session, session duration, entry/exit pages)
- Feature adoption heatmap: a visual map of all FlowFlex modules/features showing what percentage of companies and users have used each feature at least once in the last 30 days — colour-coded from cold (unused) to hot (heavily used); primary tool for FlowFlex product team to identify underused features

### Advanced
- Funnel analysis: define a sequence of events (e.g. "Visited pricing page → Started trial → Created first project → Invited team member") and see the drop-off percentage at each step — filterable by date range, company segment, and plan tier
- Funnel comparison: compare funnel conversion rates across two time periods or two user cohorts (e.g. companies that onboarded in Q1 vs Q2) to measure the impact of onboarding changes
- Retention cohort analysis: group users by when they first triggered a key event (e.g. first login) and track what percentage return after 1 day, 7 days, 14 days, 30 days, 60 days, 90 days — cohort grid rendered as a colour-coded table (darker = better retention)
- Session heatmaps: click and scroll heatmaps on specific pages — captures click coordinates and scroll depth using privacy-compliant tracking (no keystroke recording, no form field content capture); requires a page-specific heatmap activation toggle
- User journey paths: given a starting event and an ending event, show the most common sequences of events in between — helps identify unexpected paths users take before reaching a goal
- Company-level adoption view: for the Admin panel, show feature adoption grouped by company — which companies have activated which modules, and within those modules which features they actually use

### AI-Powered
- Funnel drop-off analysis: given a funnel with significant drop-off at a specific step, AI analyses the events preceding the drop-off for behavioural patterns (e.g. "Users who drop off at step 3 are 3× more likely to have viewed the pricing page multiple times") — surfaces hypotheses for product improvement
- Anomaly detection integration: spikes or drops in key event volumes (e.g. `module_activated` dropping 40% week-on-week) are detected by the Anomaly Detection module and surfaced as alerts on the Product Analytics dashboard
- Feature adoption recommendations: AI identifies the 3 FlowFlex features with the highest correlation to long-term retention (based on cohort analysis) and surfaces them as "High-impact features to promote" — feeds into in-app guidance and onboarding flow prioritisation

## Data Model

```erDiagram
    pa_events {
        ulid id PK
        ulid company_id FK "nullable for FlowFlex-internal events"
        ulid user_id FK "nullable"
        string session_id
        string event_name
        json properties
        string url
        string referrer
        string device_type
        string country_code
        timestamp created_at
    }

    pa_sessions {
        string session_id PK
        ulid company_id FK "nullable"
        ulid user_id FK "nullable"
        timestamp started_at
        timestamp ended_at
        integer event_count
        string entry_url
        string exit_url
        integer duration_seconds
    }

    pa_funnels {
        ulid id PK
        ulid company_id FK "nullable — null = FlowFlex platform funnel"
        string name
        json steps
        timestamps created_at/updated_at
    }

    pa_funnel_results {
        ulid id PK
        ulid funnel_id FK
        date computed_for_date
        json step_counts
        json step_conversion_rates
        timestamps created_at/updated_at
    }

    pa_heatmap_pages {
        ulid id PK
        ulid company_id FK "nullable"
        string url_pattern
        boolean is_active
        timestamps created_at/updated_at
    }

    pa_heatmap_events {
        ulid id PK
        ulid page_id FK
        string session_id
        string event_type "click|scroll"
        integer x_percent
        integer y_percent
        integer scroll_depth_percent
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `pa_events` | High-volume table — partition by `created_at` month in PostgreSQL for query performance |
| `pa_events.properties` | JSON — indexed with `GIN` index for `jsonb` queries |
| `pa_events.company_id` | Null for FlowFlex admin-level events (not tied to a specific tenant) |
| `pa_sessions.session_id` | UUID v4 generated client-side, stored in `sessionStorage`; a new session begins after 30 minutes of inactivity |
| `pa_funnels.steps` | JSON array: `[{event_name, label, filter_properties}]` — up to 10 steps per funnel |
| `pa_funnel_results` | Pre-computed nightly by `ComputeFunnelResultsJob` — not computed on-demand due to query cost |
| `pa_heatmap_events.x_percent` | Click position as % of page width (0–100) — not pixel coordinates, for resolution-independence |

## Permissions

```
analytics.product.view-events
analytics.product.manage-funnels
analytics.product.view-heatmaps
analytics.product.view-retention
analytics.product.export-data
```

## Filament

- **Custom page:** `ProductAnalyticsDashboardPage` — the primary view; sections:
  - **Event Volume** — line chart of total events per day for the last 30 days, filterable by event name; top 10 events by volume table
  - **Feature Adoption Heatmap** — visual grid of all FlowFlex modules and sub-features with adoption percentage colour coding (uses CSS grid, not a map library — simpler and faster to render)
  - **Active Sessions** — real-time count of active sessions (using Reverb presence channel count)
- **Custom page:** `FunnelAnalysisPage` — funnel builder (define steps via a drag-and-drop event selector) and funnel results visualization (horizontal bar chart showing count and conversion rate at each step); funnel comparison toggle
- **Custom page:** `RetentionCohortPage` — cohort selector (first event, date range), retention grid rendered as an HTML table with CSS background-color heat mapping (green = high retention), downloadable as CSV
- **Custom page:** `HeatmapPage` — page selector dropdown, click heatmap rendered as a SVG overlay on a screenshot of the page (screenshot captured via a scheduled Playwright job), scroll depth histogram
- **Nav group:** Data (analytics panel)
- **Widget:** `ProductAdoptionWidget` on analytics dashboard — shows top 3 most-used features and top 3 least-used features across all tenants (admin view)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Mixpanel | Event tracking, funnel analysis, retention cohorts |
| Amplitude | Product analytics, user journey paths, feature adoption |
| PostHog | Event tracking, funnels, session recording, heatmaps |
| Heap | Auto-capture event tracking, retroactive funnels |
| FullStory | Session heatmaps (click/scroll, privacy-compliant) |
| Pendo | Feature adoption tracking, in-app guidance analytics |

## Related

- [[dashboards]]
- [[anomaly-detection]]
- [[reports]]
- [[kpi-metrics]]
- [[data-connectors]]

## Implementation Notes

### V1 Scope: Internal Only
V1 tracks FlowFlex's own platform usage only. The `pa_events` tracking is instrumented in the Filament panel via a JavaScript event hook on navigation and key user actions. The `company_id` on events identifies which tenant performed the action — the Admin panel can filter events by tenant for per-company analysis.

V2 (company SDK): FlowFlex companies can embed the tracking SDK (`//cdn.flowflex.com/analytics/v1/pa.js`) into their own products. Companies use a public API key to identify their company. Events from external products include a `source: 'external_sdk'` property and are displayed in the company's own analytics panel view — separate from the FlowFlex internal events.

### Event Ingestion at Scale
`pa_events` is a high-volume write table. Optimisations:
1. **Async ingestion endpoint:** `POST /api/v1/analytics/events` is handled by a lightweight controller that publishes events to a Redis queue, not directly to PostgreSQL — a background worker flushes in batches of 500 every 5 seconds
2. **Table partitioning:** partition `pa_events` by `created_at` month using PostgreSQL range partitioning — create new partitions 2 months ahead via a scheduled job
3. **Retention policy:** default 12-month data retention; events older than the retention period are deleted by a monthly cleanup job
4. **GIN index on properties:** `CREATE INDEX pa_events_properties_idx ON pa_events USING GIN(properties jsonb_path_ops)` — enables efficient filtering by property values in funnel queries

### Heatmap Privacy
Click heatmaps must never capture form field values, text selections, or keyboard input. The tracker captures only click coordinates (as page percentage, not pixel) and element tag/class for click maps. Text inputs are explicitly excluded from click tracking via a selector exclusion list. The heatmap feature requires explicit opt-in per page (not enabled by default) to prevent accidental capture of sensitive UI.

### Funnel Computation
Funnel results are pre-computed nightly (not on-demand) due to the cost of multi-step event sequence queries on large `pa_events` tables. The `ComputeFunnelResultsJob` runs at 02:00 UTC, computes the last 30-day window for all active funnels, and writes to `pa_funnel_results`. On-demand funnel computation (for small datasets or admin users) is available but gated behind a "compute now" button with a warning that it may be slow.
