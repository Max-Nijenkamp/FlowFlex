---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.sla
status: planned
priority: p2
depends-on: [support.tickets, core.billing, core.rbac, core.notifications, core.settings]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [queues, websockets, custom-pages]
tables: [sup_sla_policies, sup_sla_targets, sup_sla_events]
permission-prefix: support.sla
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# SLA Management

Service Level Agreement policies: first-response and resolution time targets per priority. Tracks compliance and alerts before breach.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/support/tickets\|support.tickets]] | SLA timers live on tickets |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, breach alerts, business hours/timezone |

---

## Core Features

- SLA policy: name, per-priority targets (first response time, resolution time)
- Business hours: targets count only during configured business hours (per company timezone, from settings)
- SLA timer per ticket: counts up from ticket creation; pauses on `waiting_on_customer`
- Breach detection: ticket exceeds target → flag + notification to assignee and manager
- Breach warning: notify when 80% of SLA time elapsed (once)
- SLA assignment: by ticket category or priority
- SLA compliance report: % of tickets meeting first-response and resolution targets
- Pause/resume math: paused intervals from ticket state-transition audit log *(assumed: pause windows derived from status timestamps)*

---

## Data Model

### sup_sla_policies — id, company_id (indexed), name, business_hours_only (bool), deleted_at
### sup_sla_targets — id, sla_policy_id FK, company_id, priority, first_response_minutes, resolution_minutes; unique `(sla_policy_id, priority)`
### sup_sla_events

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), ticket_id FK | ulid | |
| type | string | first_response_met / first_response_breached / resolution_met / resolution_breached / warning_sent |
| occurred_at | timestamp | unique `(ticket_id, type)` — each event once per ticket |

---

## DTOs

### CreateSlaPolicyData — name (required), business_hours_only, targets[{priority, first_response_minutes (min:1), resolution_minutes (> first_response)}]

## Services & Actions

- `SlaService::elapsedMinutes(Ticket $ticket): int` — wall clock minus paused windows minus out-of-business-hours (when flag)
- `SlaService::check(Ticket $ticket): void` — emits met/warning/breached events (once each, unique-guarded) + notifications
- `SlaService::complianceReport(CarbonImmutable $from, CarbonImmutable $to): SlaComplianceData`
- Met events recorded synchronously by ticket transitions (first reply / resolve)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CheckSlaTimersCommand` | default | every 5 min | unique `(ticket, event type)` constraint — warnings/breaches once |

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SlaPolicyResource` | #1 CRUD resource | per-priority targets repeater |
| `SlaMonitorPage` | #8-style live custom page | tickets nearing breach, Reverb broadcast updates |
| `SlaComplianceWidget` | #6 widget | compliance % |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('support.sla.view-any') && BillingService::hasModule('support.sla')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`support.sla.view` · `support.sla.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Elapsed minutes excludes waiting_on_customer windows
- [ ] Business-hours-only policy counts only business hours (timezone fixture)
- [ ] Warning at 80% fires once; breach fires once
- [ ] First reply before target → met event; after → breached
- [ ] Compliance % over fixtures
- [ ] Check command idempotent (5-min reruns)

---

## Build Manifest

```
database/migrations/xxxx_create_sup_sla_policies_table.php
database/migrations/xxxx_create_sup_sla_targets_table.php
database/migrations/xxxx_create_sup_sla_events_table.php
app/Models/Support/{SlaPolicy,SlaTarget,SlaEvent}.php
app/Data/Support/{CreateSlaPolicyData,SlaComplianceData}.php
app/Services/Support/SlaService.php
app/Console/Commands/Support/CheckSlaTimersCommand.php
app/Filament/Support/Resources/SlaPolicyResource.php
app/Filament/Support/Pages/SlaMonitorPage.php
app/Filament/Support/Widgets/SlaComplianceWidget.php
database/factories/Support/SlaPolicyFactory.php
tests/Feature/Support/{SlaTimerTest,SlaBreachTest}.php
```

---

## Related

- [[domains/support/tickets]]
- [[architecture/websockets]]
- [[architecture/queue-jobs]]
