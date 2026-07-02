---
domain: communications
module: whatsapp
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# WhatsApp — Unknowns

## Assumed Items

- One config / one number per company for v1 *(assumed)*.
- Media handled via `core.files` with a MIME whitelist *(assumed detail)*.

## Open Questions

> [!warning] UNVERIFIED
> **Provider choice (360dialog vs Twilio vs Meta Cloud API) is undecided** — a build-time ADR is mandatory before build. Pricing, EU data residency, and template-approval latency differ per BSP. See [[decisions]].

- Multi-number support (several WhatsApp numbers per company) — deferred past v1?
- Template category rules + Meta's per-message pricing (utility free in-window since Jul 2025) — how surfaced to the user?
- How is the 24h-window countdown displayed in the inbox composer?
- Number registration/verification UX — in-app vs. provider portal hand-off?

## Related

- [[_module]] · [[decisions]] · [[../../../product/positioning]]
