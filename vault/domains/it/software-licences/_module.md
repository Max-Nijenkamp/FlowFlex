---
domain: it
module: software-licences
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Software Licences

Track software subscriptions and licences: seats, costs, renewal dates, and utilisation. Avoid over-buying and surprise renewals. Owns `it_licences` and `it_licence_assignments`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | seat assignments per employee; consumes `EmployeeOffboarded` |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, renewal alerts |
| Soft | [[../../finance/expenses/_module\|finance.expenses]] | spend visibility (report link only, read-only *(assumed)*) |

---

## Core Features

- Licence record: software name, vendor, total seats, used seats (computed), cost per seat, billing cycle, renewal date
- Seat assignment: which employees use which licence
- Renewal alerts: notify 30 days before renewal date (once per cycle)
- Utilisation: used vs total seats — flag waste (brick/money)
- Cost tracking: monthly/annual spend per tool
- Renewal calendar + total software spend dashboard
- `EmployeeOffboarded` → that employee's seats flagged for reclamation

---

## Build Manifest

```
database/migrations/xxxx_create_it_licences_table.php
database/migrations/xxxx_create_it_licence_assignments_table.php
app/Models/IT/{Licence,LicenceAssignment}.php
app/Data/IT/{CreateLicenceData,AssignSeatData}.php
app/Services/IT/LicenceService.php
app/Actions/IT/{AssignSeatAction,RevokeSeatAction}.php
app/Listeners/IT/FlagSeatsForReclaimListener.php
app/Console/Commands/IT/LicenceRenewalAlertCommand.php
app/Filament/IT/Resources/LicenceResource.php
app/Filament/IT/Widgets/LicenceRenewalWidget.php
database/factories/IT/{LicenceFactory,LicenceAssignmentFactory}.php
tests/Feature/IT/LicenceTest.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Over-capacity assignment rejected; duplicate active seat rejected
- [ ] Offboard flags that employee's seats
- [ ] Renewal alert once per cycle; resets when date changes
- [ ] Utilisation + waste math (brick/money)

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `EmployeeOffboarded` | [[../../hr/employee-profiles/_module\|hr.profiles]] | `FlagSeatsForReclaimListener` flags that employee's active seats for reclamation (writes own `it_licence_assignments`) |
| Feeds (soft) | spend visibility | [[../../finance/expenses/_module\|finance.expenses]] | Report-only read of licence cost totals; no write, no event *(assumed)* |

**Data ownership:** `it.licences` writes only `it_licences` + `it_licence_assignments`; the offboarding effect writes its own `it_licence_assignments` rows, never HR's tables — all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

---

## Related

- [[architecture|software-licences.architecture]]
- [[data-model|software-licences.data-model]]
- [[security|software-licences.security]]
- [[decisions|software-licences.decisions]]
- [[unknowns|software-licences.unknowns]]
- [[features/licence-record|licence-record feature]]
- [[features/seat-assignment|seat-assignment feature]]
- [[features/renewal-alerts|renewal-alerts feature]]
- [[features/offboarding-seat-reclaim|offboarding-seat-reclaim feature]]
- [[../../hr/employee-profiles/_module|hr.profiles]]
- [[../../finance/expenses/_module|finance.expenses]]
- [[../../../architecture/event-bus]]
