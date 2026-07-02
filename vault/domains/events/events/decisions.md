---
domain: events
module: events
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events — Decisions

## ADR: Status via spatie/laravel-model-states

- **Context:** Events move through a defined lifecycle with side effects (landing goes live, registration opens, registrants notified on cancel).
- **Decision:** Model the lifecycle as a state machine (`Draft → Published → Live → Completed | Cancelled`) rather than a plain string, so transitions and guards are enforced in code.
- **Consequences:** Transitions are auditable and idempotent; the scheduled `EventLifecycleCommand` drives time-based transitions.

## ADR: Public landing is Vue + Inertia, not Filament

- **Context:** The event landing page is an unauthenticated marketing/registration surface.
- **Decision:** Build it as a public Vue + Inertia page ([[../../../architecture/ui-strategy]] row #16), rate-limited, rather than a Filament page.
- **Consequences:** Public surface is decoupled from the admin panel; registration form (owned by registrations) is embedded on the same page.

## ADR: Virtual link revealed to confirmed registrants only *(assumed)*

- **Context:** Virtual/hybrid events carry a join link that should not leak to the public.
- **Decision (assumed):** `virtual_link` is withheld from the public landing and shown only after a registrant is confirmed.
- **Consequences:** Reveal mechanism must be enforced in the registrations/portal read path. Unconfirmed — see [[unknowns]].
