---
type: domain-index
domain: Core Platform
panel: app
panel-path: /app
panel-color: Slate
color: "#4ADE80"
---

# Core Platform

Core Platform contains the twelve cross-cutting features that are included with every FlowFlex subscription. No module activation or extra billing is required — they are always available to every company from day one.

Core Platform sits at the base of every other domain. The audit log records actions taken by all other modules. Notifications delivers alerts from every domain. The billing engine gates access to optional domain modules. The setup wizard gets companies running in minutes. All other domain modules depend on at least one Core Platform module.

## Modules

| Module | File | Module Key |
|---|---|---|
| Audit Log | [[audit-log]] | `core.audit` |
| Notifications | [[notifications]] | `core.notifications` |
| Setup Wizard | [[setup-wizard]] | `core.setup` |
| Data Import | [[data-import]] | `core.import` |
| File Storage | [[file-storage]] | `core.files` |
| API Clients | [[api-clients]] | `core.api` |
| Sandbox Environments | [[sandbox-environments]] | `core.sandbox` |
| Billing Engine | [[billing-engine]] | `core.billing` |
| Internationalisation | [[i18n]] | `core.i18n` |
| Webhooks | [[webhooks]] | `core.webhooks` |
| Module Marketplace | [[module-marketplace]] | `core.marketplace` |
| Company Settings | [[company-settings]] | `core.settings` |
| Data Privacy | [[data-privacy]] | `core.privacy` |
| Survey Builder | [[survey-builder]] | `core.surveys` |

## Conventions

- All Core modules are **always active** — they cannot be deactivated by a company
- Audit log records actions from every other domain — no domain may write directly to `audit_logs`; all must go through `AuditLogger::log()`
- Company Settings is the source of truth for locale, timezone, currency, and branding — all other modules inherit from it
