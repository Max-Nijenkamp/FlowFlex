---
domain: support
module: support-analytics
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — Architecture

## Services & Actions

- `SupportAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): SupportMetricsData` — aggregate queries (volume, first-response/resolution averages, breakdowns, agent table), no N+1
- `RecordCsatAction::run(CsatResponseData $data): void` — public token path, stamps `responded_at`
- `SendCsatSurveyListener` on `TicketResolved` — queued (`ShouldQueue` + `WithCompanyContext`), creates a `sup_csat_responses` row + mails the link (v1 consumer until marketing P3)

Metrics aggregate read-only from `sup_tickets`, `sup_ticket_replies`, `sup_sla_events` — this module writes only `sup_csat_responses`.

---

## Events

### Consumes: `TicketResolved` (from support.tickets)

| Handler | Action |
|---|---|
| `SendCsatSurveyListener` | create `sup_csat_responses` (token, unanswered) + queue `CsatSurveyMail` |

Contract in [[../../../architecture/event-bus]]. `company_id` is a scalar in the payload.

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:support:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament Artifacts

**Nav group:** Analytics

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SupportDashboardPage` | #6 dashboard page + apex charts | date-range filter; widget polling 60s |
| `TicketVolumeWidget` / `CsatWidget` / `AgentPerformanceWidget` / `BusyHoursWidget` | #6 widgets | volume, CSAT, per-agent, heat-map |

Public CSAT page: Vue + Inertia `/csat/{token}` — ui-strategy row #16, `CsatController` + `resources/js/Pages/Csat/Respond.vue`.

**Access contract:** panel artifacts gate on `canAccess() = Auth::user()->can('support.analytics.view') && BillingService::hasModule('support.analytics')` per [[../../../architecture/filament-patterns]] #1 — the dashboard states it explicitly. Public CSAT runs under a token-only guard (see [[./security]]).

---

## Search & Realtime

No search. No realtime — widgets poll every 60s. SLA compliance widget hidden when [[../sla/_module|support.sla]] inactive.

See [[./security]] for the public CSAT guard (HIGH).
