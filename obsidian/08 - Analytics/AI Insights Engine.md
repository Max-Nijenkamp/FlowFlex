---
tags: [flowflex, domain/analytics, ai, insights, phase/6]
domain: Analytics
panel: analytics
color: "#0E7490"
status: planned
last_updated: 2026-05-08
---

# AI Insights Engine

Ask your business data questions in plain English. "Why did revenue drop last month?" or "Which sales rep has the best conversion rate?" — AI queries the data, explains what it found, and shows the chart. No SQL, no BI analyst needed.

**Who uses it:** Executives, department heads, all users for self-serve analytics
**Filament Panel:** `analytics`
**Depends on:** Core, [[Custom Dashboards]], [[Report Builder]], [[Data Warehouse & Export]], [[AI Infrastructure]]
**Phase:** 6

---

## Features

### Natural Language Queries

- Text input: ask any business question in plain English (or Dutch, German, French)
- AI interprets intent → generates SQL query against FlowFlex data warehouse
- Executes query → returns results as: table, bar chart, line chart, pie chart, or text summary
- Follows up: "why?", "break it down by department", "compare to last year"
- Multi-turn conversation: ask clarifying follow-ups in context

### Auto-Generated Insights

- Morning digest: AI sends daily summary of top 5 business metrics with change vs yesterday
- Anomaly detection: "Revenue dropped 23% on Tuesday — here's a possible cause"
- Trend alerts: "Your churn rate has been increasing for 3 weeks"
- Opportunity signals: "Deals in the pipeline this quarter are 40% above last quarter"
- Digest recipients configurable per role: CEO gets revenue + HR, Sales manager gets CRM metrics

### Insight Cards

- Pushed to user's home dashboard
- Dismissible: user can dismiss or mark as acted-upon
- Drill down: click insight → opens full query result
- Share: send insight to a colleague or Slack channel
- Save: add insight to a custom dashboard as a permanent widget

### Data Connections

- All FlowFlex domains: HR, Finance, CRM, Projects, Marketing, Ecommerce, Operations, LMS
- Cross-domain queries: "employees who are also CRM contacts" — join across modules
- Real-time: queries run against live data (no separate copy)
- RBAC respected: AI only returns data the user is permitted to see

### Chart Generation

- Auto-selects best chart type for the data shape
- Manual override: switch from bar to line, add trendline, etc.
- Download: PNG, SVG, CSV of underlying data
- Embed: add AI-generated chart to any custom dashboard
- Annotation: add text note to chart explaining context

### Explainability

- "How did you calculate this?" — AI explains the SQL logic in plain English
- Confidence indicator: high / medium / low based on data completeness
- Caveats: "Note: this excludes cancelled orders" flagged automatically

---

## Database Tables (2)

### `analytics_ai_queries`
| Column | Type | Notes |
|---|---|---|
| `user_id` | ulid FK | |
| `question` | text | natural language input |
| `generated_sql` | text | |
| `result_type` | enum | `table`, `chart`, `text` |
| `chart_config` | json nullable | type, x-axis, y-axis, etc. |
| `result_summary` | text nullable | AI explanation |
| `execution_ms` | integer nullable | |
| `row_count` | integer nullable | |
| `saved_to_dashboard` | boolean default false | |

### `analytics_ai_insights`
| Column | Type | Notes |
|---|---|---|
| `user_id` | ulid FK | |
| `type` | enum | `anomaly`, `trend`, `opportunity`, `digest` |
| `title` | string | |
| `body` | text | |
| `query_id` | ulid FK nullable | backing query |
| `severity` | enum | `info`, `warning`, `critical` |
| `dismissed_at` | timestamp nullable | |
| `acted_on_at` | timestamp nullable | |

---

## Permissions

```
analytics.ai-insights.view
analytics.ai-insights.query
analytics.ai-insights.configure-digest
analytics.ai-insights.share
```

---

## Competitor Comparison

| Feature | FlowFlex | Tableau Ask Data | Power BI Q&A | ThoughtSpot |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€) | ❌ | ❌ (€€€€) |
| Natural language → chart | ✅ | ✅ | ✅ | ✅ |
| Proactive anomaly alerts | ✅ | ❌ | partial | ✅ |
| Cross-domain joins (HR+CRM) | ✅ | partial | ✅ | ✅ |
| RBAC-filtered results | ✅ | partial | ✅ | ✅ |
| Dutch/NL language queries | ✅ | ❌ | partial | ❌ |

---

## Related

- [[Analytics Overview]]
- [[Custom Dashboards]]
- [[Report Builder]]
- [[Data Warehouse & Export]]
- [[AI Infrastructure]]
- [[KPI & Goal Tracking]]
