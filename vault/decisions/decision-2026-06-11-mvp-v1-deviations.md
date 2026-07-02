---
type: adr
date: 2026-06-11
status: decided
domain: HR, Finance, CRM
color: "#F97316"
---

# MVP v1 Implementation Deviations from Specs

## Context

The MVP gate path (15 modules across HR/Finance/CRM) was built 2026-06-11 on top of Foundation + Core. Specs were followed for data models, state machines, events (payloads character-exact per event-bus), permissions, and gating. The deviation protocol (way-of-working) requires documenting where v1 implementation deliberately trims spec scope.

## Deviations (deliberate, v1 scope)

1. **Output DTOs** — services return Eloquent models, not `*Data` output DTOs. Input DTOs (spatie/laravel-data) are used everywhere per the hard rule. Output DTOs land with the REST API layer (core.api endpoints per domain).
2. **Leave calendar** — `LeaveCalendarPage` not built: `saade/filament-fullcalendar` has no Filament 5 release (gap-filament5-plugins-unavailable). Leave list + filters cover v1; calendar lands when the plugin ships or a custom page is built.
3. **Working-days calculation** — weekends-only exclusion; public-holiday calendar arrives with the i18n holiday data *(spec marks holiday source assumed)*.
4. **Pipeline board** — stage-move buttons instead of Alpine drag-drop; Reverb broadcast deferred to the realtime pass (ui-strategy row #3 realtime default).
5. **Payroll** — payslip generation synchronous inside `processRun` (not `GeneratePayslipsJob` on the hr queue); payslip PDF + mail deferred. Selected-employee set held in cache between create and process.
6. **hr.self-service** — `in-progress`: own-data rule + service paths exist via profiles; dedicated dashboard/MyProfile pages pending.
7. **crm.quotes** — `in-progress`: model + send + token issuance built; line editor UI + public accept page (signed, guest guard) pending.
8. **Search (Meilisearch/Scout)** — employee/contact indexes not wired yet; lands as a cross-cutting search pass.
9. **Pricing** — module catalog prices in `config/flowflex.php` are *(assumed)* placeholders pending the pricing-model ADR.

## Decision

Ship the MVP gate path with these documented trims. Specs remain the product source of truth; each deviation either has a tracked landing slot (API layer, realtime pass, search pass, plugin gap) or an `in-progress` module status.

## Related

- [[00-index/status-board]]
- [[architecture/event-bus]]
- [[build/gaps/gap-filament5-plugins-unavailable]]
- [[architecture/way-of-working]] — deviation protocol
