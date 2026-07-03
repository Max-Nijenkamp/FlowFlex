---
domain: support
module: sla
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# SLA Management

Service Level Agreement policies: first-response and resolution time targets per priority. Tracks compliance and alerts before breach.

---

## Module-key

`support.sla`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.sla`  
**Tables:** `sup_sla_policies`, `sup_sla_targets`, `sup_sla_events`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tickets/_module\|support.tickets]] | SLA timers live on tickets |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] + [[../../core/company-settings/_module\|core.settings]] | gating, permissions, breach alerts, business hours/timezone |

---

## Core Features

- SLA policy: name, per-priority targets (first-response time, resolution time)
- Business hours: targets count only during configured business hours (per company timezone, from settings)
- SLA timer per ticket: counts up from ticket creation; pauses on `waiting_on_customer`
- Breach detection: ticket exceeds target → flag + notification to assignee and manager
- Breach warning: notify when 80% of SLA time elapsed (once)
- SLA assignment: by ticket category or priority
- SLA compliance report: % of tickets meeting first-response and resolution targets
- Pause/resume math derived from ticket state-transition audit log *(assumed: pause windows from status timestamps)*

See [[./features/sla-policies|SLA Policies]] and [[./features/breach-monitoring|Breach Monitoring]] features.

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

## Test Checklist

- [ ] Tenant isolation: SLA timers/events for company A never read company B tickets or settings
- [ ] Module gating: artifacts hidden when `support.sla` inactive
- [ ] Elapsed minutes excludes `waiting_on_customer` windows
- [ ] Business-hours-only policy counts only business hours (timezone fixture)
- [ ] Warning at 80% fires once; breach fires once
- [ ] First reply before target → met event; after → breached
- [ ] Compliance % over fixtures
- [ ] Check command idempotent (5-min reruns)

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `sup_tickets` status timestamps | support.tickets | timer / pause math (same domain, direct) |
| Reads | business hours + timezone | core.settings | read-only company settings |
| Feeds | breach/warning notifications | core.notifications | via notification service |

**Data ownership:** `support.sla` writes only `sup_sla_policies`, `sup_sla_targets`, `sup_sla_events`; reads ticket timestamps + company settings, never writes them ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../../../architecture/websockets]]
- [[../../../architecture/queue-jobs]]
