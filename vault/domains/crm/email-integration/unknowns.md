---
domain: crm
module: email-integration
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Integration — Unknowns

## Assumed Items

- Provider push webhooks arrive in **v1.x**; v1 is scheduled-pull only *(assumed)*.
- Merge field syntax is `{{contact.first_name}}` *(assumed)*.
- Tracking endpoints use a **per-email token** for signature validation *(assumed)*.
- Unmatched inbound emails are stored **unlinked** (no contact) *(assumed)*.
- On GDPR erasure, an erased contact's emails are unlinked and the body is purged *(assumed — personal correspondence)*.

## Open Questions

- What exactly does a per-email tracking token contain, and how is it signed/rotated?
- How are merge fields resolved for missing values (blank vs placeholder)?
- Should unmatched inbound mail be retro-linked when a matching contact is later created?
- Retention policy for tracking timestamps and raw bodies?
- Which provider webhook events (v1.x) trigger incremental sync, and how do they reconcile with the scheduled cursor?
- Behaviour when the same message appears across two connected mailboxes in the same company (cross-connection dedupe)?
