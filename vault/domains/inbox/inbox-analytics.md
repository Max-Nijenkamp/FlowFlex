---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.analytics
status: planned
color: "#4ADE80"
---

# Inbox Analytics

> Conversation volume by channel, first response time, resolution time, agent workload, CSAT from post-resolution surveys, busiest hours heatmap, and channel performance comparison — all computed from live inbox data.

**Panel:** `/inbox`
**Module key:** `inbox.analytics`

## What It Does

Inbox Analytics gives team leads a clear view of how the omnichannel inbox is performing across all connected channels. Metrics surface conversation volume, agent response speed, resolution rates, customer satisfaction (via post-resolution survey sent through the same channel the conversation happened on), and agent workload balance. All data is computed from `inbox_conversations` and `inbox_messages` — no separate ETL. Charts use Chart.js inside Filament custom widgets. Managers filter all views by date range, channel, agent, or label.

## Features

### Core
- Conversation volume trend: line chart of conversations opened, resolved, and pending over the date range (daily/weekly grouping). Segmented by channel type.
- First response time: average time from conversation creation to first outbound agent message. Segmented by channel and agent.
- Resolution time: average time from conversation creation to resolved status. Per-channel and per-agent breakdown.
- CSAT: post-resolution satisfaction survey sent via the same channel (e.g. WhatsApp message "How did we do? 1–5"). Score aggregated per agent and per channel.
- Agent workload table: per-agent breakdown of open conversations, conversations resolved today, and average response time. Helps identify overloaded agents.

### Advanced
- Channel performance comparison: bar chart comparing first response time and resolution rate across all connected channels (WhatsApp vs email vs SMS vs Instagram)
- Busiest hours heatmap: 7×24 matrix of average incoming message volume — helps plan agent shift coverage
- Label frequency chart: which labels are most applied — identifies recurring conversation topics
- Resolution rate: percentage of opened conversations that reach resolved status within the date range
- SLA-equivalent view: though inbox conversations don't have formal SLAs, this view shows % of conversations responded to within target windows (configurable thresholds: < 5 min, < 1 hr, < 4 hr, > 4 hr)
- CSV export of all metrics in the date range

### AI-Powered
- Daily digest: Claude generates a plain-English daily summary of the previous day's inbox performance with key observations (volume spikes, channels with slow response, top labels) — displayed at the top of the analytics page and optionally emailed to the team lead
- Performance anomaly alerts: AI detects statistical outliers (e.g. first response time on WhatsApp 3× higher than 7-day average) and surfaces them as inline alerts in the analytics dashboard

## Data Model

All data computed from:

- `inbox_conversations` — volume, status, channel_type, assignee_id, last_message_at, created_at
- `inbox_messages` — timestamps for first agent reply calculation, CSAT survey responses
- `inbox_conversation_labels` — label frequency

No dedicated analytics tables. Add compound indexes on `(company_id, channel_type, created_at)` and `(company_id, assignee_id, created_at)` on `inbox_conversations`.

| Derived Metric | Source |
|---|---|
| First response time | Time between `inbox_conversations.created_at` and `MIN(inbox_messages.sent_at)` where `direction = outbound AND is_private_note = false` |
| Resolution time | `inbox_conversations.updated_at` WHERE `status changed to resolved` (or a dedicated `resolved_at` column if added) |
| CSAT | Survey response stored as `inbox_messages` with `sender_type = contact` and `body IN ('1','2','3','4','5')` flagged via `is_csat_response = true` |

## Permissions

```
inbox.analytics.view
inbox.analytics.export
inbox.analytics.agent-detail
inbox.analytics.configure
inbox.analytics.insights
```

## Filament

- **Resource:** None
- **Custom pages:** `InboxAnalyticsPage` — full-page custom Filament page at `/inbox/analytics`. Tabs: Overview, Channels, Agents, Labels. Header date range picker and channel filter. Class: `App\Filament\Inbox\Pages\InboxAnalyticsPage`.
- **Widgets:** `InboxVolumeChart` (line chart by channel), `InboxResponseTimeWidget` (stat cards), `ChannelComparisonChart` (bar chart), `AgentWorkloadTable` (Filament TableWidget), `BusiestHoursHeatmap` (reused from Support domain — same Blade component, different data source), `InboxCsatWidget` (average score + distribution)
- **Nav group:** Settings (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Respond.io | Inbox analytics, agent performance |
| Freshdesk Messaging | Omnichannel conversation reports |
| Chatwoot | Reporting and analytics |
| Sprout Social | Social inbox analytics |
| Bird | Omnichannel message analytics |

## Related

- [[shared-inbox]]
- [[inbox-automations]]
- [[domains/support/support-analytics]]
- [[domains/analytics/INDEX]]

## Implementation Notes

- **CSAT survey dispatch:** When a conversation is resolved, a `SendCsatSurvey` queued job fires (if CSAT is enabled in channel settings). It sends a short survey message via the same channel adapter used for outbound conversation replies (e.g. WhatsApp message "How would you rate your experience? Reply 1–5"). Responses matching `[1-5]` from the same contact on the same conversation within 48 hours are stored as `inbox_messages` with `is_csat_response = true`.
- **Shared heatmap component:** `BusiestHoursHeatmap` Blade component accepts a `$data` 2D array (7 days × 24 hours). The Support and Inbox analytics pages each provide their own data. Rendered with Chart.js matrix plugin.
- **Performance on large data sets:** For companies with > 500K conversations, a `AggregateInboxDailyStats` scheduled daily job pre-computes per-channel, per-agent, per-day aggregates into `inbox_daily_stats` (company_id, date, channel_type, assignee_id, conversations_opened, conversations_resolved, avg_first_response_seconds, avg_resolution_seconds, csat_avg). Analytics page reads from this cache table for date ranges > 30 days; queries live data for ranges ≤ 30 days.
- **AI daily digest:** `GenerateInboxInsights` scheduled command at 06:00 UTC. Queries previous day's aggregated metrics, formats a structured data block, calls Claude to generate 3–5 observations. Stored in `inbox_ai_insights` table. Optionally dispatches `InboxDailyDigestMail` to the team lead email addresses configured in channel settings.
