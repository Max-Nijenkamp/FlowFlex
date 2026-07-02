---
domain: events
module: registrations
feature: public-registration
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Public Registration

The public attendee sign-up form embedded on the event landing page, with atomic capacity + waitlist.

## Behaviour

- Attendee submits name, email, optional ticket, and custom answers for a published event with registration open.
- Duplicate email per event is rejected (via `attendee_email_hash`).
- Atomic capacity check → `registered`; if full → `waitlisted`.
- Free event → auto-`confirmed` + confirmation email with `.ics`. Paid event → stays `registered` pending ticket purchase.
- Fires `EventRegistrationReceived` → CRM contact.
- Rate-limited + honeypot.

## UI

- **Kind**: public-vue
- **Page**: registration form embedded in the event landing (`/e/{company}/{slug}`) — Vue + Inertia, ui-strategy row #16.
- **Layout**: sticky panel — ticket select (if paid) → attendee fields → custom questions → submit; confirmation screen with add-to-calendar.
- **Key interactions**: submit → Inertia POST → optimistic pending → confirmation or waitlist notice; sold-out disables CTA.
- **States**: empty (registration open) · loading (submitting spinner) · error (duplicate email / closed / sold out → inline message) · selected (chosen ticket highlighted) · success (confirmed or waitlisted screen).
- **Gating**: guest guard; visibility gated on event published + registration-open status.

## Data

- Owns / writes: `ev_registrations` only.
- Reads: published event + capacity (Events service); ticket sales window (Tickets service, paid path).
- Cross-domain writes: NONE — the CRM contact is created by CRM's own listener on `EventRegistrationReceived` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `EventRegistrationReceived` → consumed by [[../../../crm/contacts/_module|crm.contacts]] (find-or-create contact).
- Shared entity: `crm_contacts` (written only by CRM), `ev_tickets` (read from Tickets).

## Unknowns

- Group registration in a single submission — deferred? See [[../unknowns]].
- Custom-question schema ownership (events vs. registrations) — see [[../unknowns]].

## Related

- [[../_module|Registrations]] · [[check-in]] · [[../../events/features/public-landing|Public Landing]] · [[../../../architecture/event-bus]]
