---
type: adr
date: 2026-07-03
status: decided
domain: All
color: "#F97316"
---

# Public / guest endpoint rate limiters registered

## Context

Wave 2 v3 propagation added named-rate-limiter citations to every comms / money / file / external-API action per the security convention. Workers found the registry in `architecture/security.md` defined only `login, api, api-write, password-reset, exports, panel-action, api-company` — but specs cite public-endpoint limiters that did not exist: `public-booking` (crm.scheduling), `public-apply` (hr.recruitment), `help-centre` (support.knowledge-base), `chat-widget` (support.live-chat), `csat` (support.analytics). Other public paths (NPS collector, form submit, storefront checkout, ticket purchase, speaker/supplier portals) cite a generic "guest guard limiter *(assumed)*".

## Options Considered

1. One generic `public` per-IP limiter for every guest endpoint.
2. Named per-surface limiters (chosen) — separate tuning per surface; a chatty widget doesn't consume the booking budget.
3. Leave unregistered until build — rejected: specs must cite real names.

## Decision

Register the five named per-IP limiters in `architecture/security.md` with *(assumed)* starting rates (booking/apply/csat 10/min, chat 30/min, help-centre 60/min). Remaining generic "guest guard *(assumed)*" citations should adopt one of these or add a named limiter here first, same pattern.

## Consequences

- Spec citations in support, crm, hr now resolve to registered names.
- Rates are placeholders — tune with real traffic; each is marked *(assumed)*.
- Build-time rule unchanged: any public endpoint names its limiter in security.md before code.

## Related

- [[../architecture/security|security.md]] · [[../build/gaps/gap-canaccess-verb-not-in-permission-table]] · [[decision-2026-07-02-rate-limit-and-token-hardening]]
