---
domain: projects
module: time-tracking
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — Decisions

## ADR: Time stored as minutes (int), not decimal hours

- **Context:** The v1 spec used decimal hours; the convention is integer minor units.
- **Decision:** Store `minutes_logged` as an int. UI converts for display.
- **Consequences:** No float rounding drift; report math exact. Aligns with the money-in-cents rule.

## ADR: One running timer per user

- **Decision:** `StartTimer` throws `TimerAlreadyRunningException` if the user already has a running timer.
- **Consequences:** Unambiguous "current" timer; enforced by a partial index + service check.

## ADR: Invoicing integration is CSV export in v1 *(assumed)*

- **Context:** Billable hours should reach Finance.
- **Decision:** v1 exports billable entries to CSV for manual invoice lines; an automated `TimeApproved → invoice draft` integration is a later ADR.
- **Consequences:** No cross-domain write into `fin_*`; keeps the boundary clean while deferring automation ([[unknowns]]).

## ADR: Approver ≠ owner

- **Decision:** A user cannot approve their own week.
- **Consequences:** Basic separation of duties for billable time.
