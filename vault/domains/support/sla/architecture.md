---
domain: support
module: sla
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# SLA Management — Architecture

## Services & Actions

- `SlaService::elapsedMinutes(Ticket $ticket): int` — wall-clock minus paused (`waiting_on_customer`) windows minus out-of-business-hours (when flag set)
- `SlaService::check(Ticket $ticket): void` — emits met/warning/breached events (once each, unique-guarded) + notifications to assignee/manager
- `SlaService::complianceReport(CarbonImmutable $from, CarbonImmutable $to): SlaComplianceData`
- Met events recorded synchronously by ticket transitions (first reply / resolve); warnings/breaches by the scheduled check.

Business hours + timezone read from `core.settings` (read-only).

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CheckSlaTimersCommand` | default | every 5 min | unique `(ticket, event type)` constraint — warnings/breaches fire once |

---

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SlaPolicyResource` | #1 CRUD resource | tweaks: inline-relation-repeater (per-priority targets) | validation resolution > first-response |
| `SlaMonitorPage` | #3 custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] — read-only near-breach queue grouped by urgency (green / amber / red), no drag reorder | "SLA Monitor" at `/support/sla-monitor`; Reverb broadcast on threshold crossings |
| `SlaComplianceWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | rolling compliance %; widget polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('support.sla.view') && BillingService::hasModule('support.sla')`
per [[../../../architecture/filament-patterns]] #1. `SlaMonitorPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| SLA policy + targets CRUD (form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| SLA event emission (met / warning / breach) | n/a | append-only insert into `sup_sla_events`; the unique `(ticket, event type)` constraint is the fire-once guard, not a lock — the scheduled check is idempotent |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

No search. Realtime: `SlaMonitorPage` receives Reverb broadcasts on `company.{id}.support` for tickets crossing warning/breach thresholds.

See [[./security]] for the access contract + permissions.
