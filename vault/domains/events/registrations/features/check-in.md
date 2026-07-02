---
domain: events
module: registrations
feature: check-in
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Check-In

On-site attendee check-in via QR scan or manual name search; confirmed-only.

## Behaviour

- Each confirmed registration carries a unique `qr_code` (uuid) generated on confirmation and delivered via the ticket/confirmation email.
- Staff scan the QR (camera) or paste the token / search by name → `CheckInAction` transitions `confirmed → attended` and stamps `checked_in_at`.
- Only `confirmed` registrations can check in; invalid, foreign-company, or already-attended QRs are rejected.
- Manual name search is the fallback when a QR is missing (dead phone, walk-up).

## UI

- **Kind**: custom-page
- **Page**: "Check-In" (`/app/events/registrations/check-in`) — `CheckInPage` (Livewire), ui-strategy row #7.
- **Layout**: full-screen scan mode — camera viewfinder + large token input; below, a name/email search fallback and a live "checked in / expected" counter.
- **Key interactions**: scan QR → instant validate → green "checked in" flash or red reject reason; manual search → confirm identity → check in. Sub-3-second target.
- **States**: empty (no registrations for event) · loading (camera init) · error (invalid/foreign/already-attended QR → red toast with reason) · success (green flash + counter increment).
- **Gating**: `events.registrations.view-any` to open; `events.registrations.check-in` to perform.

## Data

- Owns / writes: `ev_registrations` only (`status`, `checked_in_at`).
- Reads: own registrations scoped to the selected event.
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Unknowns

- Offline check-in (cache attendee list, queue scans, sync on reconnect) is a strong differentiator but not in the source spec — see [[../unknowns]] and [[../../_opportunities]].
- On-demand badge printing on check-in — not specified.

## Relations

- Consumes: nothing.
- Feeds: `attended` status feeds [[../../event-analytics/_module|Event Analytics]] attendance rate (read).
- Shared entity: none.

## Related

- [[../_module|Registrations]] · [[public-registration]] · [[../../event-analytics/_module|Event Analytics]]
