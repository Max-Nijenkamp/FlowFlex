---
domain: workplace
module: visitor-management
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management — Decisions

> Reconstructed from the flat source spec. Ratify during the v2 rebuild.

## ADR: External visitor PII is encrypted

- **Context:** Visitors are external people; name + email are personal data held for a purpose (arrival, compliance) with a retention limit.
- **Decision:** `wp_visitors.name` + `wp_visitors.email` use the `encrypted` cast on `text` columns. Workplace keeps its `encrypted-fields` frontmatter for this module only.
- **Consequences:** Encrypted columns are not plaintext-searchable → kiosk lookup decrypts today's expected set in memory *(assumed)*; a design cost accepted for privacy.

## ADR: Kiosk is a dedicated role, not a public route *(assumed)*

- **Context:** Reception self-service check-in must be usable by walk-ins without exposing the whole panel.
- **Decision:** Kiosk runs on a locked device under a `workplace.visitors.kiosk` role session; lookup/check-in are rate-limited per device/IP.
- **Consequences:** No open unauthenticated endpoint; a public-vue tablet screen can front it behind the same scoped guard.

## ADR: 12-month PII purge *(assumed retention)*

- **Decision:** `PurgeVisitorsCommand` deletes visitor PII older than 12 months daily.
- **Consequences:** Bounded retention for GDPR; the exact window is a configurable default pending legal input.

## ADR: Declaration (NDA) is an optional hard gate

- **Decision:** When the NDA toggle is on, check-in is blocked until `declaration_accepted_at` is stamped (checkbox + text, no e-signature *(assumed)*).
- **Consequences:** Simple compliance capture; upgrade to real e-sign is a future option.
