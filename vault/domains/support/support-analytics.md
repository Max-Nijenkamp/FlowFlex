---
type: module
domain: Support & Help Desk
panel: support
module-key: support.analytics
status: planned
color: "#4ADE80"
---

# Support Analytics

Support performance dashboards: CSAT, resolution time, ticket volume trends, agent performance, and SLA compliance.

## Core Features

- CSAT score: customer satisfaction rating after resolution (1–5 stars)
- Ticket volume trends: created vs resolved over time
- Average first-response time and resolution time
- Tickets by category, priority, channel breakdown
- Agent performance: tickets handled, avg resolution time, CSAT per agent
- SLA compliance rate (from SLA module)
- Backlog trend: open ticket count over time
- Busiest hours/days heat-map for staffing decisions

## Data Model

No additional tables. Aggregates from `sup_tickets`, `sup_ticket_replies`, `sup_sla_events`, plus a `sup_csat_responses` table:

| Table | Key Columns |
|---|---|
| `sup_csat_responses` | company_id, ticket_id, rating, comment, responded_at |

## Filament

**Nav group:** Analytics

- `SupportDashboardPage` (custom dashboard) — chart widgets via `leandrocfe/filament-apex-charts`
- Date range filter

## Related

- [[domains/support/tickets]]
- [[domains/support/sla]]
- [[architecture/performance]] — cache heavy aggregations
