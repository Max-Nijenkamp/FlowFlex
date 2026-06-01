---
type: module
domain: IT & Security
panel: it
module-key: it.licences
status: planned
color: "#4ADE80"
---

# Software Licences

Track software subscriptions and licences: seats, costs, renewal dates, and utilisation. Avoid over-buying and surprise renewals.

## Core Features

- Licence record: software name, vendor, total seats, used seats, cost per seat, billing cycle, renewal date
- Seat assignment: which employees use which licence
- Renewal alerts: notify before renewal date
- Utilisation: used vs total seats (identify waste)
- Cost tracking: monthly/annual spend per tool
- Renewal calendar
- Total software spend dashboard
- Flag unused seats for reclamation

## Data Model

| Table | Key Columns |
|---|---|
| `it_licences` | company_id, software_name, vendor, total_seats, cost_per_seat_cents, billing_cycle, renewal_date, currency |
| `it_licence_assignments` | licence_id, company_id, employee_id, assigned_at, revoked_at |

## Filament

**Nav group:** Licences

- `LicenceResource` — list, create, assign seats
- `LicenceRenewalWidget` — upcoming renewals
- Utilisation shown per licence

## Cross-Domain

- Renewal costs can feed Finance (recurring expense forecasting)
- Consumes `EmployeeOffboarded` → flag seats for reclamation

## Related

- [[domains/it/access-provisioning]]
- [[domains/finance/expenses]]
