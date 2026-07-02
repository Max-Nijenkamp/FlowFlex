---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Domain Merges

---

## Context

The original vault had 35 standalone domains. Several of these overlapped significantly with MVP domains or were too small to justify separate panels.

## Decision

Merge four former domains into their parent:

| Merged Domain | Into | Rationale |
|---|---|---|
| FP&A (budgeting, forecasting, scenario modelling) | Finance & Accounting | Budgeting and forecasting are Finance features, not a separate domain. Added as modules `finance.budgets` and `finance.forecasting`. |
| Subscription Billing | Core Platform | The billing engine is core platform infrastructure — not a business module companies activate separately. Absorbed into `core.billing`. |
| Pricing Management | CRM & Sales | Price books and CPQ are sales tools — they live alongside Quotes and Deals. Added as `crm.pricing`. |
| Omnichannel Inbox | Communications | Shared inbox and broadcast messaging belong together in one `/comms` panel. Panel slug: `/comms`. |

## Consequences

- Domain count: 35 → 21 active domains + 10 deferred
- Former `/fpa`, `/billing`, `/pricing`, `/inbox` panel paths removed
- All modules from merged domains preserved as sub-modules in parent domain
- No data model impact — module keys remain unique

## Related

- [[domains/_overview]]
- [[domains/finance/_index]]
- [[domains/core/_index]]
- [[domains/crm/_index]]
- [[domains/communications/_index]]
