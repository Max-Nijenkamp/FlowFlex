---
domain: hr
module: time-attendance
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Unknowns — Time & Attendance

Assumptions and open questions carried from the source spec. Resolve before/at build; each is an overridable default (ADR) unless build-blocking.

## Assumptions `*(assumed)*`

- Shift soft-dep (`hr.shifts`) planned-vs-actual comparison is display only.
- Standard workday for overtime detection defaults to 8h (from company settings).
- `(company_id, employee_id, date)` unique — one time entry per day for v1; multiple entries would need separate rows / relaxed index later.
- `LogTimeEntryData.date` may not be in the future.

## Open Questions

- Whether the one-entry-per-day index should be relaxed for multi-shift days (see assumption above).

## Unverified

- Whether `hr.payroll` exists to consume `TimesheetApproved`; until built the event fires unconsumed.
- Company week-start setting and standard-workday setting sources are referenced but not yet defined here.

## Related

- [[_module]]
- [[data-model]]
