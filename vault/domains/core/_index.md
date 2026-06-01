---
type: domain-index
domain: Core Platform
panel: app
color: "#4ADE80"
---

# Core Platform

Cross-cutting features included with every FlowFlex subscription — always active, no extra billing. Audit log, notifications, billing engine, module marketplace, company settings, and supporting infrastructure. All other domains depend on at least one Core module.

**Panel:** `/app` (Slate)

---

## Modules

| Module | Key | Status | Description |
|---|---|---|---|
| [[domains/core/company-settings\|Company Settings]] | `core.settings` | planned | Timezone, locale, currency, branding, workspace config |
| [[domains/core/billing-engine\|Billing Engine]] | `core.billing` | planned | Module subscription management, invoice generation, Stripe integration |
| [[domains/core/module-marketplace\|Module Marketplace]] | `core.marketplace` | planned | Activate/deactivate modules, view pricing, manage subscriptions |
| [[domains/core/audit-log\|Audit Log]] | `core.audit` | planned | Full activity trail across all domains — all writes go through `AuditLogger` |
| [[domains/core/notifications\|Notifications]] | `core.notifications` | planned | In-app notification inbox, email alerts, notification preferences |
| [[domains/core/rbac\|Roles & Permissions]] | `core.rbac` | planned | Role management UI, permission assignment, Filament Shield integration |
| [[domains/core/file-storage\|File Storage]] | `core.files` | planned | File upload, media library, storage paths by company |
| [[domains/core/data-import\|Data Import]] | `core.import` | planned | CSV import for employees, contacts, products — mapping UI, validation, preview |
| [[domains/core/webhooks\|Webhooks]] | `core.webhooks` | planned | Configurable outbound webhooks on domain events |
| [[domains/core/api-clients\|API Clients]] | `core.api` | planned | API key management for Sanctum tokens, scopes, rotation |
| [[domains/core/setup-wizard\|Setup Wizard]] | `core.setup` | planned | First-login company onboarding wizard: name, branding, invite team, first module |
| [[domains/core/data-privacy\|Data Privacy]] | `core.privacy` | planned | GDPR tooling: DSAR requests, consent logs, data export, erasure queue |
| [[domains/core/i18n\|Internationalisation]] | `core.i18n` | planned | Locale management, translation keys, date/number format by company |
| [[domains/core/invitation-system\|Invitation System]] | `core.invitations` | planned | Team member invite flow: email → token → registration → role assignment |
| [[domains/core/health-monitoring\|Health Monitoring]] | `core.health` | planned | spatie/laravel-health checks, Pulse dashboard, Horizon monitoring, Sentry |

---

## Absorbed Domains

**Subscription Billing** (formerly standalone) — billing features live in [[domains/core/billing-engine]].

---

## Conventions

- All Core modules are always active — cannot be deactivated
- No domain may write directly to `audit_logs` — must call `AuditLogger::log()`
- Company Settings is the source of truth for locale, timezone, currency, branding — all other modules read from it
- `BillingService::hasModule(string $key)` is the single gating check for every optional module

## Related Patterns

- [[architecture/auth-rbac]]
- [[architecture/data-model]]
- [[product/pricing-model]]
