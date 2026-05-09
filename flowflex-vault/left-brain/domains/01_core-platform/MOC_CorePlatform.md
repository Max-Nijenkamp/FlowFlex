---
type: moc
domain: Core Platform
panel: admin
cssclasses: domain-admin
phase: 1
color: "#111827"
last_updated: 2026-05-09
---

# Core Platform — Map of Content

The features every domain module depends on: notifications, file storage, API layer, billing engine, setup wizard, audit log. Authentication and multi-tenancy foundations are handled in Phase 0 (Foundation).

**Panel:** `admin`  
**Phase:** 1  
**Migration Range:** `010000–099999`  
**Colour:** Gray `#111827`  
**Status:** 📅 Planned

---

## Module Map

```mermaid
graph TD
    BILLING["Module Billing Engine"]
    NOTIFY["Notifications & Alerts"]
    API["API & Integrations Layer"]
    FILES["File Storage"]
    WIZARD["Setup Wizard & Guided Onboarding"]
    AUDIT["Audit Log"]
    IMPORT["Data Import Engine"]

    WIZARD --> BILLING
    NOTIFY --> API
    IMPORT --> FILES
    AUDIT --> BILLING
```

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| RBAC Management UI | 1 | planned | Company owner UI for creating roles, assigning domain.module.action permissions — built on top of Spatie Permission which is bootstrapped in Foundation |
| Module Billing Engine | 1 | planned | Stripe subscriptions, module toggle enforcement, plan limits, usage metering |
| Notifications & Alerts | 1 | planned | In-app, email, push, SMS, webhook — cross-domain notification dispatch |
| API & Integrations Layer | 1 | planned | REST API, OAuth 2.0, webhooks, rate limiting, SDK generation |
| File Storage | 1 | planned | S3-compatible, media library, signed URLs, company-scoped paths |
| Setup Wizard & Guided Onboarding | 1 | planned | 6-step first-login wizard, guided checklist, progress tracking |
| [[notification-preferences\|Notification Preferences]] | 1 | planned | Per-user channel preferences, digest settings, quiet hours |
| [[audit-log\|Audit Log]] | 1 | planned | Immutable activity trail, per-record history, export |
| [[data-import-engine\|Data Import Engine]] | 1 | planned | CSV/Excel bulk import for all entities, column mapping, rollback |
| [[sandbox-environment\|Sandbox Environment]] | 1 | planned | Per-tenant staging environment, production clone, safe testing |
| Company & Workspace Settings | 1 | planned | Company name, branding, timezone, locale, currency — managed in workspace panel settings |
| i18n & Localisation | 1 | planned | Multi-language UI (EN, NL, DE, FR, ES), per-user locale, number/date/currency formatting |

---

## Key Architecture Concepts Used

- Built on top of Foundation scaffold — see [[MOC_Foundation]]
- [[module-system]] — Interface/Service pattern bootstrapped here

---

## Related

- [[MOC_Domains]]
- [[entity-company]]
- [[entity-user]]
- [[entity-module-subscription]]
