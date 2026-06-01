---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Fully Spec All Phase 1/2/3 Modules Up Front

---

## Context

The earlier vault rebuild deliberately left Phase 2 and Phase 3 domains as stubs (`_index.md` only), to avoid premature speccing. The user requested all Phase 1, 2, and 3 domain functionality be fully written out so nothing is missing.

## Decision

All 172 modules across Phase 1 (MVP), Phase 2, and Phase 3 are now fully specced with: What It Does, Core Features, Data Model (tables + key columns, ERD for complex ones), Filament section (CRUD vs custom page, nav group, widgets), cross-domain events, and related links.

Only the 10 deferred domains remain as one-paragraph stubs (real-estate, field-service, psa, esg, travel, community, plg, ethics, partners, risk).

## Consequences

- Every active module is build-ready: `/flowflex:start {module-key}` returns a complete briefing for any of the 172 modules
- Cross-domain event contracts are documented consistently (e.g. `EmployeeHired`, `CheckoutCompleted`, `CourseCompleted`, `FormSubmissionReceived`)
- Shared entities flagged where domains overlap (Operations ↔ Procurement POs/GRN; CRM ↔ Legal contracts; Support ↔ IT helpdesk)
- Some specs note ADRs still needed at build time (e.g. WhatsApp provider choice)
- Maintenance cost: 172 specs to keep current. Mitigated by specs linking to canonical architecture patterns rather than repeating them.

## Vault Totals

- 250 markdown files total
- 172 active module specs + 31 domain index files + 21 architecture files + 10 patterns + product/frontend/build tracking

## Related

- [[domains/_overview]]
- [[build/STATUS]]
- [[build/decisions/decision-2026-06-01-domain-defers]]
