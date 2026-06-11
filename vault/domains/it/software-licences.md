---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.licences
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [finance.expenses]
fires-events: []
consumes-events: [EmployeeOffboarded]
patterns: [money, events]
tables: [it_licences, it_licence_assignments]
permission-prefix: it.licences
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Software Licences

Track software subscriptions and licences: seats, costs, renewal dates, and utilisation. Avoid over-buying and surprise renewals.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | seat assignments per employee |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, renewal alerts |
| Soft | [[domains/finance/expenses\|finance.expenses]] | spend visibility (report link only *(assumed)*) |

---

## Core Features

- Licence record: software name, vendor, total seats, used seats (computed), cost per seat, billing cycle, renewal date
- Seat assignment: which employees use which licence
- Renewal alerts: notify 30 days before renewal date (once per cycle)
- Utilisation: used vs total seats — flag waste
- Cost tracking: monthly/annual spend per tool (brick/money)
- Renewal calendar
- Total software spend dashboard
- `EmployeeOffboarded` → that employee's seats flagged for reclamation

---

## Data Model

### it_licences

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| software_name / vendor | string | |
| total_seats | int min 1 | |
| cost_per_seat_cents | bigint | |
| billing_cycle | string | monthly / annual |
| renewal_date | date | |
| currency | string(3) | |
| renewal_alerted_at | timestamp nullable | per-cycle once-guard, cleared on renewal-date change |
| deleted_at | timestamp nullable | |

### it_licence_assignments — id, licence_id FK, company_id, employee_id FK, assigned_at, revoked_at nullable, reclaim_flagged_at nullable; unique active `(licence_id, employee_id)`

---

## DTOs

### CreateLicenceData — software_name, vendor, total_seats (min:1), cost_per_seat_cents (min:0), billing_cycle (in set), renewal_date, currency
### AssignSeatData — licence_id (free seats available — "All seats are in use."), employee_id (no active seat on this licence)

## Services & Actions

- `AssignSeatAction` / `RevokeSeatAction`
- `LicenceService::utilisation(Licence $l): array{used: int, total: int, waste_cents: int}`
- Listener `FlagSeatsForReclaimListener` on `EmployeeOffboarded`
- `LicenceRenewalAlertCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `LicenceRenewalAlertCommand` | notifications | daily | `renewal_alerted_at` once-guard |

---

## Filament

**Nav group:** Licences

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LicenceResource` | #1 CRUD resource | utilisation bar, seat assignment relation |
| `LicenceRenewalWidget` | #6 widget | renewals next 60d + flagged seats |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('it.licences.view-any') && BillingService::hasModule('it.licences')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`it.licences.view-any` · `it.licences.manage` · `it.licences.assign`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Over-capacity assignment rejected; duplicate active seat rejected
- [ ] Offboard flags that employee's seats
- [ ] Renewal alert once per cycle; resets when date changes
- [ ] Utilisation + waste math (brick/money)

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

## Related

- [[domains/it/access-provisioning]]
- [[domains/finance/expenses]]
- [[architecture/event-bus]]
