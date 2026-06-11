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
| 2026-06-10 | [[build/decisions/decision-2026-06-10-no-public-registration\|No public self-registration — invite-only, staff-created companies]] | decided | Core / Foundation |
| 2026-06-10 | [[build/decisions/decision-2026-06-10-all-filament-hybrid-ui\|All-Filament hybrid UI: Filament inside panels, Vue+Inertia outside]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-security-contract-hardening\|Security contract hardening: mandatory canAccess(), webhook verification, guest guards, encrypted PII]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-ui-strategy-new-rows\|UI strategy rows 17–19: gallery/directory, heat-map/matrix, spatial/floor-map]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-static-analysis-without-larastan\|Static analysis: plain PHPStan + @property docblocks (Larastan deferred)]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-flat-namespace-foldering\|Flat namespace foldering — no Core/Foundation build-phase subfolders]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-mvp-v1-deviations\|MVP v1 implementation deviations from specs (documented trims)]] | decided | HR, Finance, CRM |
| 2026-06-11 | [[decision-2026-06-11-2fa-and-mandatory-email-verification\|Self-Service 2FA + Mandatory Email Verification]] | decided | All |
| 2026-06-11 | [[decision-2026-06-11-perceived-performance-standard\|Perceived-Performance Standard]] | decided | All |
| 2026-06-11 | [[build/decisions/decision-2026-06-11-owner-only-settings-modules\|Owner-only company settings + module marketplace]] | decided | Core |

---

## How to Add a Decision

Run `/flowflex:decision ["title"] [status=decided|proposed]` to create a new ADR file and add it here.

Or create manually: `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md` with frontmatter `type: adr`, `color: "#F97316"`, `status: decided|proposed`.
