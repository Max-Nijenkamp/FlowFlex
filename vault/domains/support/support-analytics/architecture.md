---
domain: support
module: support-analytics
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SupportDashboardPage` | #6 dashboard page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | date-range filter; `leandrocfe/filament-apex-charts`; widget polling 60s |
| `TicketVolumeWidget` / `CsatWidget` / `AgentPerformanceWidget` / `BusyHoursWidget` | #6 dashboard widgets | [[../../../architecture/patterns/page-blueprints#Dashboard]] (BusyHours = apexcharts heat-map) | volume, CSAT, per-agent, busy-hours heat-map; SLA compliance widget hidden without `support.sla` |

Public CSAT page: Vue + Inertia `/csat/{token}` — ui-strategy row #16, `CsatController` + `resources/js/Pages/Csat/Respond.vue`.

**Access contract (mandatory):** every panel artifact gates on
`canAccess() = Auth::user()->can('support.analytics.view') && BillingService::hasModule('support.analytics')`
per [[../../../architecture/filament-patterns]] #1. `SupportDashboardPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. The public CSAT page is Vue+Inertia per [[../../../architecture/ui-strategy]] under a **token-only guard** (no panel session) with a named rate limiter — not a Filament artifact (see [[./security]]).

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| CSAT survey row creation (`TicketResolved` listener) | n/a | append-only insert into `sup_csat_responses` (token, unanswered); the unique `(ticket_id)` / token constraint makes the queued listener idempotent |
| CSAT public response submit | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the response row, guard `responded_at IS NULL`, then stamp rating/comment — a replayed or double submit is rejected |
| Metrics aggregation / dashboard | n/a | read-only aggregate queries over Tickets / SLA tables; writes nothing |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

No search. No realtime — widgets poll every 60s. SLA compliance widget hidden when [[../sla/_module|support.sla]] inactive.

See [[./security]] for the public CSAT guard (HIGH).
