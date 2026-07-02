---
domain: finance
module: invoicing
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Tax fallback** — without [[../tax-management/_module|finance.tax]], a single default tax rate from company settings applies. *(assumed)*
- **Tax rounding** — tax rounds per line, then lines sum (line-level rounding). *(assumed)*
- **Default payment terms** — `fin_customers.payment_terms_days` defaults to 14 from settings. *(assumed)*
- **Numbering concurrency** — gap-free sequential numbers per company are guarded by an advisory lock at first send. *(assumed)*

UNVERIFIED: the exact recurring-schedule cutover behaviour (e.g. whether the day-of-month is pinned or drifts) is not enumerated in the spec. UNVERIFIED: `SendPaymentReminderCommand` "per-bucket once" flag storage is deferred to the AR module and not specified here.
