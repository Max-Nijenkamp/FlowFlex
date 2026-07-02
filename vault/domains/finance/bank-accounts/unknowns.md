---
domain: finance
module: bank-accounts
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Bank Accounts — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Match window** — suggested matches use exact amount within a ±5-day date window. *(assumed)*
- **IBAN masking** — an `iban_last4` string is stored for masked display alongside the encrypted full value. *(assumed)*

UNVERIFIED: the exact CSV column-mapping UX (how the date format is captured/validated) is described at a high level only. UNVERIFIED: the rate-limiter threshold (N imports per company per minute) is cited as a requirement but no concrete N is fixed in the spec. UNVERIFIED: how `current_balance_cents` is reconciled against the GL balance over time (the spec only specifies a point-in-time `balanceComparison`).
