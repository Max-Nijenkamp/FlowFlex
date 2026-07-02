---
domain: customer-success
module: nps
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# NPS — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Manual sending only v1** — scheduled / post-interaction / lifecycle-triggered NPS is deferred; v1 is manual send to a chosen audience.
- **Shared suppression** — `send()` honours a shared marketing/comms suppression list; the exact ownership of that list is assumed (comms domain).
- **Token expiry by single-use, not time** — tokens don't expire on a clock; they're invalidated once answered. A time-box is not modelled.
- **Detractor alert as notification** — a detractor response raises `core.notifications`, not a cross-domain domain event.
- **CSM recipient = CRM account `owner_id`** — shared assumption across CS ([[../health-scores/unknowns]]).

## Open Questions

- Survey fatigue / suppression window: should NPS respect a global "no survey within N days" window shared with other survey tools? Competitor gap noted in [[../_opportunities]]; not specified for v1.
- Anonymous vs identified responses — v1 ties every response to a contact (via token). A fully anonymous mode is not modelled.
- Re-send / reminder to non-responders — not specified.

## Implementation Notes

- The `(survey_id, contact_id)` unique constraint enforces one response per recipient per survey.
- `latestForAccount` drives the health sentiment factor — index `(company_id, account_id, responded_at)` matters.
