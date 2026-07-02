---
domain: communications
module: sms-channel
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# SMS Channel — Unknowns

## Assumed Items

- Twilio is the first provider *(assumed)*.
- One virtual number per company for v1 *(assumed)*.
- Segment estimate: 160 GSM / 70 unicode chars per segment *(assumed)*.
- `cost_cents` stored in `comms_messages.meta` *(assumed — depends on inbox meta column)*.
- Provider sends the STOP confirmation reply *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> A2P 10DLC / carrier registration (US) and short-code vs. long-code choice are undocumented — required before real US sending, adds cost + onboarding friction.

- Multi-number support beyond v1?
- Inbound MMS (media) handling — supported or dropped?
- Cost currency + FX when the provider bills in USD but the company reports in EUR.
- STOP synonyms / localisation (e.g. Dutch "STOP") — provider-handled or app-handled?

## Related

- [[_module]] · [[decisions]]
