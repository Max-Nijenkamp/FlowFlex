---
domain: it
module: software-licences
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Software Licences — Architecture

See also [[_module|software-licences._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/event-bus]], [[../../../architecture/packages]], [[../../../architecture/ui-strategy]].

---

## Services & Actions

- `AssignSeatAction::run(AssignSeatData $data): LicenceAssignment` — rejects if no free seats ("All seats are in use.") and if the employee already holds an active seat on this licence; writes `it_licence_assignments`.
- `RevokeSeatAction::run(LicenceAssignment $assignment): void` — sets `revoked_at`, frees the seat.
- `LicenceService::utilisation(Licence $licence): array{used: int, total: int, waste_cents: int}` — used = active assignments, total = `total_seats`; `waste_cents` = (total − used) × `cost_per_seat_cents` computed via brick/money (no raw float math).
- Listener `FlagSeatsForReclaimListener` on `EmployeeOffboarded` — flags (`reclaim_flagged_at`) all of that employee's active `it_licence_assignments` for the event's `company_id`. `implements ShouldQueue` + `WithCompanyContext`.
- `LicenceRenewalAlertCommand` — daily console command; sends renewal notifications 30 days out, once per cycle.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `LicenceRenewalAlertCommand` | notifications | daily | `renewal_alerted_at` once-guard — set when the alert fires, cleared when `renewal_date` changes so the next cycle can alert again |

The `renewal_alerted_at` timestamp guarantees at-most-one alert per renewal cycle. Changing `renewal_date` resets it (see [[decisions|software-licences.decisions]]).

---

## Filament Artifacts

**Nav group:** Licences

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `LicenceResource` | #1 CRUD resource | tweaks: custom-header-actions (assign / revoke seat) | table + form; utilisation bar (used/total, waste); seat-assignment relation manager |
| `LicenceRenewalWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | renewals in the next 60 days + seats flagged for reclaim; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('it.licences.view-any') && BillingService::hasModule('it.licences')`
per [[../../../architecture/filament-patterns]] #1. This module has no custom pages (resource + widget) and no
public/portal surface. See [[security|software-licences.security]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Licence CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Seat assignment (capacity + duplicate guard) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the licence, re-count active seats against `total_seats`, reject over-capacity / duplicate active seat, then write per [[../../../architecture/patterns/states]] (capacity decrement) |
| Seat revoke | Optimistic | `updated_at` stale-check on the assignment row ([[../../../architecture/patterns/optimistic-locking]]) |
| Renewal alert / offboard reclaim (command + listener) | n-a | schedule/event-driven single writer under `WithCompanyContext`; `renewal_alerted_at` / `reclaim_flagged_at` once-guards prevent duplicates |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

Pattern reference: [[../../../architecture/filament-patterns]], [[../../../architecture/ui-strategy]].
