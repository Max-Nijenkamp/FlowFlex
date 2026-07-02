---
domain: marketing
module: campaigns
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns — Unknowns

Parent: [[_module]]

## Assumed Items

- `failed` state is resumable *(assumed)*.
- A/B winner is chosen by open rate *(assumed)*; click-rate winner + auto-send-to-remainder not specced.
- Per-channel/provider rate limits not fixed — depends on [[../../foundation/email-setup/_module|foundation.email]] transport.

## Open Questions

> [!warning] UNVERIFIED
> Marketing-consent model per contact (who may lawfully be emailed for marketing vs. transactional) is undocumented at the campaigns layer — it relies on the segment already being consent-clean. A first-class consent flag / ledger is a candidate (see [[../_opportunities]]).

- A/B: does the winner auto-send to the un-sent remainder, or is the split the whole audience?
- Block-based email builder vs. Tiptap-only for v1.
- Send-time optimisation (per-recipient best hour) — out of scope v1?
- Should a send log a touch on the recipient's CRM timeline? Currently no event fired.

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
