---
domain: finance
module: tax-management
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **OSS report-only** — OSS reporting is a summary report only; no OSS filing integration in v1. *(assumed)*
- **VIES failure-tolerant** — VIES network failure yields "unverified" and never blocks a customer/supplier save. *(assumed)*

UNVERIFIED:
- The spec does not enumerate which VAT control accounts (GL accounts) output/input tax post to.
- The exact rounding boundary between `TaxCalculator` line-level rounding and invoicing totals is described as "consistent with invoicing" but not specified numerically.
- The default seeded tax rates/classes per company (if any) are not enumerated in the spec.
