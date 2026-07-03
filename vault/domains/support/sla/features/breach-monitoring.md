---
domain: support
module: sla
feature: breach-monitoring
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Breach Monitoring

Live watch on SLA timers with warning-before-breach alerts and a compliance widget.

## Behaviour

- `CheckSlaTimersCommand` runs every 5 min: for each open ticket, `SlaService::check` computes elapsed minutes (excluding paused + out-of-hours windows) and emits `warning_sent` at 80% and `*_breached` past target — each once (unique `(ticket, type)` guard).
- Breach/warning notify assignee (+ manager on breach) via `core.notifications`.
- `SlaMonitorPage` shows tickets nearing breach live (Reverb); `SlaComplianceWidget` shows the rolling compliance %.

## UI

- **Kind**: custom-page — `SlaMonitorPage`, a live near-breach board (+ a companion widget).
- **Page**: "SLA Monitor" (`/support/sla-monitor`) — Filament custom Page + Reverb, ui-strategy row #8-style; `SlaComplianceWidget` (#6) on the support dashboard.
- **Layout**: list/board of at-risk tickets sorted by time-to-breach, colour-coded (green/amber/red); compliance % header.
- **Key interactions**: ticket crosses threshold → live row recolour + toast; click ticket → open in inbox.
- **States**: empty (nothing at risk → "all within SLA" state) · loading (skeleton) · error (retry) · selected (ticket row highlighted).
- **Gating**: `support.sla.view`.

## Data

- Owns / writes: `sup_sla_events` (met/warning/breach rows).
- Reads: `sup_tickets` timestamps/status (same domain); `core.settings` business hours.
- Cross-domain writes: none — notifications go through `core.notifications` service ([[../../../../security/data-ownership]]).

## Relations

- Consumes: ticket status transitions (same-domain) drive met events.
- Feeds: breach/warning notifications → core.notifications; compliance feeds [[../../support-analytics/_module|support.analytics]] SLA widget.
- Shared entity: `sup_tickets` (read), company settings (read).

## Test Checklist

### Unit
- [ ] Elapsed-minutes math excludes `waiting_on_customer` and (when flagged) out-of-business-hours windows, honouring company timezone
- [ ] 80% threshold triggers `warning_sent`; past target triggers `*_breached`

### Feature (Pest)
- [ ] `CheckSlaTimersCommand` emits each of met / warning / breach at most once per ticket (unique `(ticket, type)` guard); 5-min reruns are idempotent
- [ ] Breach notifies assignee + manager, warning notifies assignee, via `core.notifications` — no direct cross-domain write
- [ ] Compliance report returns correct % over a fixture window
- [ ] Tenant isolation: the command never crosses company boundaries

### Livewire
- [ ] `SlaMonitorPage` `canAccess()` denies without `support.sla.view` / inactive module
- [ ] A ticket crossing a threshold recolours its row live (Reverb) and sorts by time-to-breach

## Unknowns

- Reopen timer reset behaviour *(assumed continue)* — [[../unknowns]].

## Related

- [[../_module|SLA Management]] · [[./sla-policies]] · [[../../../../architecture/websockets]]
