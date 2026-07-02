---
domain: support
module: sla
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SlaPolicyResource` | #1 CRUD resource | per-priority targets repeater |
| `SlaMonitorPage` | #8-style live custom page | tickets nearing breach, Reverb broadcast updates |
| `SlaComplianceWidget` | #6 widget | compliance % |

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('support.sla.view') && BillingService::hasModule('support.sla')` per [[../../../architecture/filament-patterns]] #1 — the custom monitor page states it explicitly.

---

## Search & Realtime

No search. Realtime: `SlaMonitorPage` receives Reverb broadcasts on `company.{id}.support` for tickets crossing warning/breach thresholds.

See [[./security]] for the access contract + permissions.
