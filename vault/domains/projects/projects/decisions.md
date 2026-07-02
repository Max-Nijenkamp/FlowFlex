---
domain: projects
module: projects
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — Decisions

## ADR: Member-scoped visibility with a `view-any` override

- **Context:** Some projects are private to their team; leadership needs a full view.
- **Decision:** Listing is scoped to project members by default; `projects.projects.view-any` bypasses the scope.
- **Consequences:** Enforced in the service query, not just Filament, so the rule holds for API/exports too.

## ADR: Actuals read from time entries, degrade to zero

- **Context:** Budget tracking needs actual hours/cost, but the Time module may be inactive.
- **Decision:** `ProjectService::actuals()` reads time entries when `projects.time` is active; returns 0 otherwise (no error).
- **Consequences:** Projects works standalone; budget "actual" columns show 0 until time tracking is enabled.

## ADR: Client link is a read-only CRM foreign id *(assumed)*

- **Context:** Projects can be tied to a CRM account/contact.
- **Decision:** Store `client_account_id`; resolve name/details through CRM's read API. Projects never writes `crm_*` tables.
- **Consequences:** Honours data-ownership; internal projects simply leave the link null.

## ADR: At-risk threshold >15pt behind *(assumed)*

- **Decision:** Health is `at-risk` when completion % trails elapsed-timeline % by more than 15 points, `off-track` beyond 30 *(assumed)*.
- **Consequences:** Tunable constant; documented as assumed pending product sign-off ([[unknowns]]).
