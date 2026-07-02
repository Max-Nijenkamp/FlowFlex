---
domain: finance
module: accounts-payable
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Payable — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Approval threshold** — a single amount threshold (company settings) routes above-threshold bills to `finance.ap.approve-large`. *(assumed)*
- **`voided` state** — bills can be voided from `draft`/`approved`, reversing any posting. *(assumed)*
- **SEPA export** — v1 payment runs export a batch list; `pain.001` CSV/XML is deferred. *(assumed)*
- **Payment-run currency** — bills in a single run share one currency. *(assumed)*

No build-blocking unknowns identified.

UNVERIFIED:

- The threshold value and where exactly it lives in company settings are not specified — only that a single threshold exists.
- Early-payment discount mechanics (how `early_discount_percent` / `early_discount_until` combine, and the exact GL treatment of the discount) are not fully defined.
- 3-way match tolerances (exact quantity/price variance allowed before `MatchFailedException`) are not specified.
- The batch-list export format (columns, file type) for v1 payment runs is unspecified.

See [[decisions]].
