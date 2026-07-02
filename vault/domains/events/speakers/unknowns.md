---
domain: events
module: speakers
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — Unknowns

## Assumed Items

- `AssignSpeakerAction` sends an invite notification mail *(assumed)* — the template/channel is unspecified.
- The self-submit link replaces a full speaker "portal" *(assumed)*.
- `logistics` is stored unencrypted despite containing travel/contact detail *(assumed)* — no encryption requirement documented.

## Open Questions

- Should the `submit_token` expire, and can it be regenerated/revoked per speaker?
- Does declining an assignment auto-notify the organizer / free the session slot?
- Should logistics (which may hold travel PII / phone) be encrypted like attendee PII?
- Is there a call-for-papers / proposal intake flow, or is submission invite-only? (Not in the source spec.)
