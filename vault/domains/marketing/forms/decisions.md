---
domain: marketing
module: forms
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms — Decisions

Parent: [[_module]]

## ADR: Submissions stored regardless of CRM

- **Decision:** A submission is always persisted to `mkt_form_submissions` even if CRM is inactive or contact creation fails; contact linkage happens asynchronously via the event.
- **Consequences:** No lead is lost to a downstream outage; `contact_id` may fill in later.

## ADR: Contact creation is event-driven (data-ownership)

- **Decision:** Forms fires `FormSubmissionReceived`; CRM's own listener finds-or-creates the contact and writes CRM tables. Forms never writes CRM.
- **Consequences:** Clean bounded context; forms works without CRM (degraded — no contact linkage) ([[../../../security/data-ownership]]).

## ADR: Public submit is CSRF-exempt with origin allow-list

- **Decision:** Cross-site embeds preclude CSRF tokens; the route is CSRF-exempt but company-scoped by slug, throttled per IP, honeypot-guarded, and origin-checked.
- **Consequences:** Embeddable anywhere while bounding abuse. See [[security]].

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
