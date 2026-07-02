---
domain: finance
module: accounts-receivable
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Receivable — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **`fin_invoices.last_dunning_level`** — dunning tracking column added by this module to the invoicing-owned invoices table. *(assumed)*
- **`fin_customers.credit_limit_cents`** — credit-limit column added by this module to the invoicing-owned customers table. *(assumed)*

No build-blocking unknowns identified.

UNVERIFIED:

- The spec lists credit-limit tracking as a core feature but defines no enforcement behavior (warn vs block on over-limit, where the check fires). Only the column is specified.
- Dunning escalation copy/templates (friendly → firm → final notice) are referenced by `email_template` key but not enumerated.
- The spec does not state whether AR reads `fin_customers` from invoicing or owns its own customer table; the `credit_limit_cents` column addition implies the former, but this is not explicit.

See [[decisions]].
