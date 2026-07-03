---
domain: support
module: support-analytics
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Support Analytics

Support performance dashboards: CSAT, resolution time, ticket volume trends, agent performance, and SLA compliance. Owns CSAT capture (the v1 consumer of `TicketResolved`).

---

## Module-key

`support.analytics`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.analytics`  
**Tables:** `sup_csat_responses`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tickets/_module\|support.tickets]] | all metrics; consumes `TicketResolved` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../sla/_module\|support.sla]] | compliance widget; hidden without it |

---

## Core Features

- CSAT score: customer satisfaction rating after resolution (1–5 stars) — survey link mailed on `TicketResolved` (owned here; marketing CSAT in P3 supersedes mail design *(assumed)*)
- Ticket volume trends: created vs resolved over time
- Average first-response time and resolution time
- Tickets by category, priority, channel breakdown
- Agent performance: tickets handled, avg resolution time, CSAT per agent
- SLA compliance rate (from the SLA module)
- Backlog trend: open ticket count over time
- Busiest hours/days heat-map for staffing decisions

See [[./features/support-dashboard|Support Dashboard]] and [[./features/csat-survey|CSAT Survey]] features.

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

## Test Checklist

- [ ] Tenant isolation: metrics + CSAT for company A never aggregate company B tickets
- [ ] Module gating: artifacts hidden when `support.analytics` inactive
- [ ] `TicketResolved` sends survey once; duplicate response rejected (unique token/ticket)
- [ ] CSAT average + per-agent math over fixtures
- [ ] First-response/resolution averages over fixtures
- [ ] Backlog trend counts open tickets per day correctly
- [ ] Public CSAT rate-limited

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `TicketResolved` | support.tickets | `SendCsatSurveyListener` creates a `sup_csat_responses` row + mails link |
| Reads | `sup_tickets`, `sup_ticket_replies`, `sup_sla_events` | support.tickets / support.sla | aggregate metrics (read-only) |
| Public | CSAT submission | unauthenticated visitors | token-only, rate-limited |

**Data ownership:** `support.analytics` writes only `sup_csat_responses`; all other metrics aggregate read-only from Tickets/SLA tables. The `TicketResolved` listener writes only this module's own table ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../sla/_module|support.sla]]
- [[../../../architecture/caching]]
- [[../../../architecture/event-bus]]
