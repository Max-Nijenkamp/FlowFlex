---
type: module
domain: Support & Help Desk
panel: support
module-key: support.analytics
status: planned
color: "#4ADE80"
---

# Support Analytics

> Volume trends, first response and resolution time averages, CSAT scores, agent performance table, channel breakdown, tag frequency, and busiest-hours heatmap — all computed from live ticket data.

**Panel:** `/support`
**Module key:** `support.analytics`

## What It Does

Support Analytics gives support managers a real-time view of team performance and customer satisfaction. All metrics are derived directly from the `support_tickets` and `support_ticket_messages` tables — there is no separate analytics storage or ETL pipeline. Charts are rendered with Chart.js inside Filament custom widgets. Managers can filter all views by date range, channel, agent, team, or tag. The CSAT section aggregates post-resolution satisfaction ratings. The agent performance table ranks agents by tickets handled, average first response time, and CSAT score, enabling fair and data-driven coaching conversations.

## Features

### Core
- Ticket volume trend: line chart showing tickets opened, closed, and pending over the selected date range (daily/weekly/monthly grouping)
- First response time: average time from ticket creation to first non-internal agent reply. Segmented by priority and team.
- Resolution time: average time from ticket creation to `resolved_at`. Segmented by priority.
- CSAT score: aggregate of all `csat_score` values from tickets closed in the date range. Distribution chart (% positive, % negative, % no response).
- Agent performance table: per-agent breakdown of tickets handled (opened/closed), average first response time, average resolution time, and CSAT score. Sortable columns.

### Advanced
- Channel breakdown: pie chart of ticket volume by channel (email / portal / phone / chat / API). Helps identify where inbound volume is concentrated.
- Tag frequency: bar chart of the top 20 tags across tickets in the date range — reveals recurring issues and knowledge base gaps.
- Busiest hours heatmap: 7×24 grid (day × hour) showing average ticket volume per time slot — identifies when staffing should be highest.
- SLA performance chart: met vs breached rate over time, segmented by SLA policy. Links to `support_sla_breaches` data.
- Date range presets: Today, Yesterday, Last 7 days, Last 30 days, This month, Last month, Custom range.
- CSV export: export the agent performance table and volume data for a given date range.

### AI-Powered
- Automated insights: once per day, Claude analyses the previous day's ticket data and generates a plain-English summary with observations (e.g. "Billing tag tickets increased 40% this week — consider adding a knowledge base article") pinned to the top of the analytics page
- Trend anomaly detection: AI flags statistical anomalies in ticket volume (e.g. a 3× spike on a Tuesday) and suggests likely causes based on ticket content

## Data Model

All data is computed at query time from:

- `support_tickets` — volume, status, priority, channel, assignee, resolved_at, first_response_at, csat_score
- `support_ticket_messages` — message timestamps for response time calculation
- `support_sla_breaches` — SLA performance data
- `support_ticket_tags` + `support_tags` — tag frequency

No dedicated analytics tables. Queries use database aggregation (`AVG`, `COUNT`, `GROUP BY`, date trunc functions). For performance on large ticket volumes (> 100K tickets), add a database index on `(company_id, created_at, status)` and `(company_id, assignee_id, created_at)`.

| Derived Metric | Source Query |
|---|---|
| First response time | `AVG(first_response_at - created_at)` filtered to tickets where `first_response_at IS NOT NULL` |
| Resolution time | `AVG(resolved_at - created_at)` filtered to `resolved_at IS NOT NULL` |
| CSAT score | `AVG(csat_score)` filtered to tickets with `csat_score IS NOT NULL` |
| Tickets handled by agent | `COUNT(*)` GROUP BY `assignee_id` for tickets closed in range |

## Permissions

```
support.analytics.view
support.analytics.export
support.analytics.agent-detail
support.analytics.configure
support.analytics.insights
```

## Filament

- **Resource:** None
- **Custom pages:** `SupportAnalyticsPage` — full-page custom Filament page at `/support/analytics`. Organised in tabs: Overview, Agents, SLA, Channels, Tags. Each tab renders a set of Chart.js `StatsOverviewWidget`-style widgets and a `TableWidget`. Date range filter and export button in the page header. Class: `App\Filament\Support\Pages\SupportAnalyticsPage`.
- **Widgets:** `SupportVolumeChart` (line chart), `SupportCsatWidget` (CSAT score + distribution), `AgentPerformanceTable` (sortable Filament TableWidget), `BusiestHoursHeatmap` (custom Blade + Chart.js matrix), `SlaPerformanceChart` (bar chart met vs breached), `TagFrequencyChart` (horizontal bar). All widgets accept a shared date-range filter propagated via Livewire events.
- **Nav group:** Settings (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk Explore | Support reporting and analytics |
| Freshdesk Analytics | Agent performance, CSAT, volume trends |
| Helpscout Reports | Response time reports, satisfaction |
| Intercom Reports | Conversation metrics |

## Related

- [[support-tickets]]
- [[sla-management]]
- [[support-analytics]]
- [[domains/analytics/INDEX]]

## Implementation Notes

- **No ETL required:** All charts compute on-the-fly using Eloquent query builder with database aggregation. For companies with ticket volumes under ~500K, this is performant with proper indexes. For high-volume companies, a daily aggregation job (`AggregateSupport DailyStats`) writes pre-computed rows to a `support_daily_stats` cache table that the analytics page reads instead of scanning raw tickets.
- **Chart.js integration:** Filament 5 includes Chart.js natively for `ChartWidget`. Custom chart types (heatmap, horizontal bar) are implemented as Blade components that receive data arrays from Livewire properties and initialise Chart.js in `@script` blocks.
- **Date range filter:** `SupportAnalyticsPage` exposes a Livewire `$dateRange` property (start, end as Carbon instances) updated by a date range picker in the page header. All child widgets listen for the `date-range-updated` Livewire event and re-run their queries. Filters are stored in the session so they persist on page reload.
- **CSV export:** Implemented as a Filament `Action` on the page that runs an `ExportSupportAnalytics` queued job (using Laravel Excel) and streams the file via a temporary signed URL.
- **AI insights:** A daily `GenerateSupportInsights` scheduled job at 06:00 UTC queries the previous day's ticket data, formats a structured prompt, and calls Claude to produce 3–5 plain-English observations. Output is stored in `support_ai_insights` (ulid, company_id, date, insights_text, generated_at) and displayed as a Filament InfoList panel at the top of `SupportAnalyticsPage`.
