---
type: decision-index
color: "#F97316"
---

# Decision Log

Architectural decisions made during the build. One file per decision.

---

## Decision Log

| Date | Decision | Status | Domain |
|---|---|---|---|
| 2026-06-01 | [[build/decisions/decision-2026-06-01-hybrid-service-pattern\|Hybrid service pattern: Actions + Interface→Service]] | decided | All |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-domain-merges\|Domain merges: FPA→Finance, Billing→Core, Pricing→CRM, Inbox+Comms]] | decided | All |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-domain-defers\|Domain defers: 10 domains moved to deferred status]] | decided | All |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk\|Raw Stripe SDK vs Laravel Cashier — skip Cashier]] | decided | Core/Billing |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-salary-history\|Salary history table — track compensation changes with audit trail]] | decided | HR |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-currency-precision\|Currency precision — store as ISO 4217 minor unit, use brick/money]] | decided | Finance |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-full-phase-3-spec\|Fully spec all Phase 1/2/3 modules up front]] | decided | All |
| 2026-06-01 | [[build/decisions/decision-2026-06-01-panel-consolidation\|Panel consolidation: Procurement→Operations, CS→CRM (21→19 panels)]] | decided | Operations, CRM |

---

## How to Add a Decision

Run `/flowflex:decision ["title"] [status=decided|proposed]` to create a new ADR file and add it here.

Or create manually: `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md` with frontmatter `type: adr`, `color: "#F97316"`, `status: decided|proposed`.
