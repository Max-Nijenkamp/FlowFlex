---
domain: events
module: registrations
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations — Decisions

## ADR: Attendee PII is encrypted

- **Context:** Registrations hold external attendee name/email + free-form custom answers — PII from non-users.
- **Decision:** `attendee_name`, `attendee_email`, `custom_answers` use the `encrypted` cast; a separate `attendee_email_hash` (sha256) backs the per-event uniqueness constraint.
- **Consequences:** Email lookups go through the hash; raw email is never indexed in plaintext.

## ADR: Capacity is atomic; overflow waitlists

- **Context:** Concurrent registrations at capacity could oversell.
- **Decision:** Enforce capacity with an atomic conditional update/row lock against `ev_events.capacity`; on overflow the registration becomes `waitlisted`. Cancellation promotes the first waitlisted (FIFO).
- **Consequences:** No oversell under concurrency; waitlist ordering is deterministic.

## ADR: CRM contact via event, not direct write

- **Context:** A registration should create/link a CRM contact.
- **Decision:** Fire `EventRegistrationReceived`; CRM's own listener find-or-creates the `crm_contacts` row. Registrations never writes CRM tables.
- **Consequences:** Bounded-context integrity; CRM can be inactive and registrations still work (soft dep). See [[../../../security/data-ownership]].

## ADR: Paid registrations confirm on payment

- **Context:** Free vs. paid registration confirmation timing.
- **Decision:** Free events auto-confirm (+`.ics` mail) at register time; paid events stay `registered` until Tickets calls `RegistrationService::confirm` on payment success.
- **Consequences:** Confirmation coupling with Tickets is a same-domain service call, not a cross-domain write.
