---
domain: it
module: software-licences
feature: licence-record
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Licence Record

CRUD of a software licence: name, vendor, seats, cost, billing cycle, renewal date — with a utilisation bar showing seat usage and waste.

## Behaviour

- Create / edit / delete a licence via `LicenceResource` ([[../../../../architecture/patterns/filament-resource-checklist]]).
- Fields: `software_name`, `vendor`, `total_seats` (min 1), `cost_per_seat_cents`, `billing_cycle` (monthly/annual), `renewal_date`, `currency`.
- Utilisation bar renders used vs total seats and waste (unused × cost) from `LicenceService::utilisation` (brick/money).
- Cost math and waste use brick/money over integer minor-currency amounts.

## UI

- **Kind**: simple-resource — table + form for one licence entity ([[../../../../architecture/ui-strategy]]).
- **Page**: `LicenceResource` at `/it/licences` (nav group Licences).
- **Layout**: table columns — software name, vendor, seats (used/total), cost per seat, billing cycle, renewal date, utilisation bar. Form — the seven `CreateLicenceData` fields. Seat assignments shown via relation manager ([[seat-assignment]]).
- **Key interactions**: create/edit/delete licence; filter by vendor / billing cycle; row action to open seat assignments.
- **States**: empty (no licences → "Add your first licence" CTA) · loading (skeleton table) · error (validation toast) · selected (row → edit form).
- **Gating**: view `it.licences.view-any`; create/edit/delete `it.licences.manage`; all gate `BillingService::hasModule('it.licences')`.

## Data

- Owns / writes: `it_licences` only.
- Reads: active `it_licence_assignments` (own table) for the utilisation bar.
- Cross-domain writes: none — this feature writes only its own module tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing directly; cost totals are read (report-only) by finance.expenses ([[../_module|module edges]]).
- Shared entity: none.

## Unknowns

- `*(assumed)*` billing-cycle set is monthly/annual — see [[../unknowns|software-licences.unknowns]].

## Related

- [[../_module|Software Licences]] · [[seat-assignment]] · [[renewal-alerts]] · [[../data-model|data-model]]
