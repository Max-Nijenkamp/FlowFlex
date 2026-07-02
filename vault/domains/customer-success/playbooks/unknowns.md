---
domain: customer-success
module: playbooks
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Playbooks — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **CSM = account `owner_id`** — step assignee for the `csm` role resolves to the CRM account owner. The `manager` role's resolution (team lead? account owner's manager?) is unspecified. Shared assumption across CS ([[../health-scores/unknowns]]).
- **New-customer trigger** — mapped to a CRM account lifecycle transition to "customer"; the exact lifecycle stage/event is assumed.
- **Auto-triggers poll daily, not event-driven** — `PlaybookTriggerCommand` polls signal sources rather than subscribing to events.
- **Reminders/assignment as notifications** — via `core.notifications`, not cross-domain events.
- **Cadence / recurrence** — playbooks are one-shot per trigger v1; recurring cadences (unlike QBR) are not modelled.

## Open Questions

- Should completing/cancelling a run that was auto-launched suppress re-triggering on the next poll while the underlying condition persists? The unique-active-run guard covers concurrency but not a re-trigger cooldown after completion.
- Should steps support dependencies/branching, or strictly linear order? v1 is linear by `order`.
- Health-drop trigger reads from `cs.churn` vs `cs.health` directly — [[../architecture]] routes it via the churn signal; confirm the single source.

## Implementation Notes

- One active run per (playbook, account) enforced by a partial-unique constraint — the core dedupe for both manual and auto launches.
- Templates (onboarding, renewal, at-risk recovery) seeded via `CsPlaybookTemplatesSeeder`.
