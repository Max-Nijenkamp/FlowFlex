---
domain: it
module: software-licences
feature: seat-assignment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Seat Assignment

Assign a licence seat to an employee, and revoke it. Enforces capacity and one-active-seat-per-employee rules.

## Behaviour

- Assign a seat via `AssignSeatAction` (writes `it_licence_assignments`); revoke via `RevokeSeatAction` (sets `revoked_at`).
- **Over-capacity rejected**: if active assignments ≥ `total_seats` → "All seats are in use."
- **Duplicate active seat rejected**: an employee already holding an active (non-revoked) seat on this licence cannot be assigned again — enforced by `AssignSeatData` validation and the unique `(licence_id, employee_id)` active index.
- Revoking frees the seat, allowing reassignment up to capacity.

## UI

- **Kind**: simple-resource / relation — relation manager under `LicenceResource` ([[../../../../architecture/ui-strategy]]).
- **Page**: seat-assignment relation on `LicenceResource` at `/it/licences/{licence}` (nav group Licences).
- **Layout**: table of assignments (employee, assigned_at, status active/revoked, reclaim-flagged badge); assign form picks an employee; revoke as a row action.
- **Key interactions**: assign employee → capacity + duplicate checks → row added; revoke row → `revoked_at` set; over-capacity/duplicate → inline validation error.
- **States**: empty (no seats assigned → "Assign a seat" CTA) · loading (skeleton rows) · error (over-capacity "All seats are in use." / duplicate active seat) · selected (assignment row highlighted).
- **Gating**: assign/revoke `it.licences.assign`; view `it.licences.view-any`; all gate `BillingService::hasModule('it.licences')`.

## Data

- Owns / writes: `it_licence_assignments` only.
- Reads: `hr_employees` (read-only) for the employee picker.
- Cross-domain writes: none — writes only its own module table; HR data is read-only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing directly (reclaim flagging lives in [[offboarding-seat-reclaim]]).
- Feeds: nothing.
- Shared entity: `hr_employees` owned by hr.employee-profiles; used read-only as the seat holder.

## Unknowns

- None beyond module-level assumptions — see [[../unknowns|software-licences.unknowns]].

## Related

- [[../_module|Software Licences]] · [[licence-record]] · [[offboarding-seat-reclaim]] · [[../data-model|data-model]]
