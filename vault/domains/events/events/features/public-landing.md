---
domain: events
module: events
feature: public-landing
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Public Landing Page

The unauthenticated event landing page: details, agenda, speakers, sponsors, and the registration form.

## Behaviour

- Served at `/e/{company}/{slug}` for **published+** events only; draft/unpublished → 404.
- Shows event details, featured image + gallery, the read-only agenda, confirmed speakers, and sponsor logos grouped by tier.
- Embeds the registration form (owned by [[../../registrations/_module|Registrations]]) and, for paid events, ticket selection (owned by [[../../tickets/_module|Tickets]]).
- `virtual_link` is never rendered publicly (confirmed-registrant-only reveal) *(assumed)*.
- Rate-limited; the embedded form carries a honeypot.

## UI

- **Kind**: public-vue
- **Page**: "Event Landing" (`/e/{company}/{slug}`) — Vue + Inertia, ui-strategy row #16.
- **Layout**: hero (image, title, date, venue map link) → about → agenda → speakers grid → sponsor tiers → sticky registration/ticket panel.
- **Key interactions**: select ticket → register (Inertia form POST); add-to-calendar `.ics`; venue directions link.
- **States**: empty (event with no agenda/speakers → sections hidden) · loading (skeleton hero) · error (sold out → disabled CTA; registration closed → notice) · selected (chosen ticket highlighted).
- **Gating**: guest guard (public); no permission — visibility gated purely on published status + company scope.

## Data

- Owns / writes: nothing (read-only render of `ev_events` + `ev_sessions`).
- Reads: own module's event/session data; confirmed speakers (Speakers service), sponsor logos (Sponsors service), ticket types (Tickets service).
- Cross-domain writes: registration submit is handled by the **registrations** module's own controller/service — the landing page only hosts the embedded form ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: the embedded form triggers `RegistrationService::register` (registrations module), which fires `EventRegistrationReceived`.
- Shared entity: reads Speakers, Sponsors, Tickets read models for display.

## Test Checklist

### Unit
- [ ] Landing renders published/live events only; draft/cancelled -> 404

### Feature (Pest)
- [ ] Page composes agenda, confirmed speakers, sponsors; registration form embedded when capacity remains
- [ ] Public route rate-limited on guest guard *(assumed -- registry reconcile task)*; tenant isolation via company slug

### Livewire
- (none -- public surface)

## Unknowns

- Exact surface for the confirmed-only `virtual_link` reveal (email vs. attendee portal) — see [[../unknowns]].

## Related

- [[../_module|Events]] · [[../../registrations/features/public-registration|Public Registration]] · [[../../tickets/_module|Tickets]]
