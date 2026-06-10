---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.analytics
status: planned
priority: p2
depends-on: [support.tickets, core.billing, core.rbac]
soft-depends: [support.sla]
fires-events: []
consumes-events: [TicketResolved]
patterns: [custom-pages]
tables: [sup_csat_responses]
permission-prefix: support.analytics
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Support Analytics

Support performance dashboards: CSAT, resolution time, ticket volume trends, agent performance, and SLA compliance.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/support/tickets\|support.tickets]] | all metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/support/sla\|support.sla]] | compliance widget; hidden without it |

---

## Core Features

- CSAT score: customer satisfaction rating after resolution (1–5 stars) — survey link mailed on `TicketResolved` (owned here; Marketing CSAT in P3 supersedes mail design *(assumed)*)
- Ticket volume trends: created vs resolved over time
- Average first-response time and resolution time
- Tickets by category, priority, channel breakdown
- Agent performance: tickets handled, avg resolution time, CSAT per agent
- SLA compliance rate (from SLA module)
- Backlog trend: open ticket count over time
- Busiest hours/days heat-map for staffing decisions

---

## Data Model

### sup_csat_responses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), ticket_id FK unique | ulid | one response per ticket |
| rating | int 1–5 | |
| comment | text nullable | |
| token | uuid unique | public response link |
| responded_at | timestamp nullable | null = sent, unanswered |

Other metrics aggregate from `sup_tickets`, `sup_ticket_replies`, `sup_sla_events`.

---

## DTOs

### CsatResponseData (public) — token (valid, unanswered), rating (1–5), comment? — rate-limited
Output: `SupportMetricsData` — period series + breakdowns + agent table.

## Services & Actions

- `SupportAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): SupportMetricsData` — aggregate queries, no N+1
- `RecordCsatAction::run(CsatResponseData $data): void` — public token path
- Listener: `SendCsatSurveyListener` on `TicketResolved` — queued, creates response row + mails link (per [[architecture/event-bus]]; this is the v1 consumer until Marketing P3)

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:support:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SupportDashboardPage` | #6 dashboard page + apex charts | date range filter; widget polling 60s |

Public CSAT page: Vue + Inertia `/csat/{token}` — ui-strategy row #16.

---

## Permissions

`support.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] `TicketResolved` sends survey once; duplicate response rejected (unique token/ticket)
- [ ] CSAT average + per-agent math over fixtures
- [ ] First-response/resolution averages over fixtures
- [ ] Backlog trend counts open tickets per day correctly
- [ ] Public CSAT rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_sup_csat_responses_table.php
app/Models/Support/CsatResponse.php
app/Data/Support/{CsatResponseData,SupportMetricsData}.php
app/Services/Support/SupportAnalyticsService.php
app/Actions/Support/RecordCsatAction.php
app/Listeners/Support/SendCsatSurveyListener.php
app/Mail/Support/CsatSurveyMail.php
app/Http/Controllers/CsatController.php + resources/js/Pages/Csat/Respond.vue
app/Filament/Support/Pages/SupportDashboardPage.php
app/Filament/Support/Widgets/{TicketVolumeWidget,CsatWidget,AgentPerformanceWidget,BusyHoursWidget}.php
database/factories/Support/CsatResponseFactory.php
tests/Feature/Support/{SupportAnalyticsTest,CsatFlowTest}.php
```

---

## Related

- [[domains/support/tickets]]
- [[domains/support/sla]]
- [[architecture/caching]]
- [[architecture/event-bus]]
