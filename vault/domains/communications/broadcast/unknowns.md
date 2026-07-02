---
domain: communications
module: broadcast
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast — Unknowns

## Assumed Items

- Per-channel rate limit ~100/min *(assumed)*.
- `cancelled` state exists for draft/scheduled broadcasts *(assumed — not fully specced)*.
- Open-tracking is email-only *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> Consent/marketing-permission model per channel (who may be emailed/SMSed for marketing vs. utility) is undocumented. GDPR marketing consent + WhatsApp marketing-template rules likely require an explicit opt-in flag on the audience.

- Should a broadcast log a touch on each recipient's CRM timeline (`crm.activities`)? Currently no event fired.
- Throttle numbers per provider (Resend / WhatsApp BSP / SMS) — real limits vs. the assumed 100/min.
- Personalisation beyond `{{first_name}}` — which fields, from which source, safely?
- In-app channel = `core.notifications` — delivery/open semantics for in-app?

## Related

- [[_module]] · [[decisions]]
