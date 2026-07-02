---
domain: workplace
module: visitor-management
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management — Unknowns

## Assumed Items

- Confirmation mail to the visitor on pre-registration *(assumed)*.
- Kiosk is a dedicated kiosk-role device session, not a public route *(assumed)*.
- Kiosk name lookup decrypts today's expected visitors in memory (encrypted columns not searchable) *(assumed)*.
- NDA capture is a checkbox + text, no e-signature *(assumed)*.
- 12-month PII retention *(assumed)*.
- No cross-domain event fired *(assumed)*.

## Open Questions

- Should check-in fire a `VisitorArrived` cross-domain event (feed to comms / physical access control)?
- Watchlist / block-list screening on check-in (see [[../_opportunities]] competitor gap) — in-scope for v1 or later?
- Does the kiosk need an offline / degraded mode if the device loses connectivity?
- Should badges integrate with access-control (temporary credential issue/revoke), or stay paper/PDF only?
- Is a true public-vue self-check-in page (scoped guard) built for v1, or is the reception-assisted flow enough?
- Exact GDPR retention window + whether visitors get a privacy notice at sign-in.
