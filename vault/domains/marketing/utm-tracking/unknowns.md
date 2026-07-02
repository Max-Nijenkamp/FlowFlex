---
domain: marketing
module: utm-tracking
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking — Unknowns

Parent: [[_module]]

## Assumed Items

- First-party UTM cookie, 30-day window *(assumed)*.
- Forms include UTM hidden fields that flow through `FormSubmissionReceived` *(assumed)* — the exact payload path is unconfirmed.

## Open Questions

> [!warning] UNVERIFIED
> Lawful basis for the UTM cookie under ePrivacy/PECR (consent vs. legitimate interest) is undocumented. A cookie-consent gate + the marketing consent-ledger ([[../_opportunities]]) likely need to govern whether a touch may be recorded at all.

- Multi-touch (linear / time-decay) models beyond first/last — deferred?
- How landing-page visits (pre-contact, anonymous) are stitched to a contact once they submit.
- Deal-value attribution when a contact has multiple deals across periods.

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
