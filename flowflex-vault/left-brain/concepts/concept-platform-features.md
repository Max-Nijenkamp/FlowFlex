---
type: concept
category: architecture
last_updated: 2026-05-09
---

# Platform-Level Features

Cross-cutting features that apply to the entire FlowFlex platform, not specific to any one domain. These must be designed in from the start — retrofitting them is expensive.

---

## Multi-Language / i18n

**Status:** Phase 1 (must be in from day 1)

- UI translations via Laravel `lang/` files + Vue i18n
- Minimum EU languages: EN, NL, DE, FR, ES
- `spatie/laravel-translatable` for content (CMS pages, email templates)
- User-level locale preference stored on `users.locale`
- Company-level default locale on `companies.locale`
- Number, date, currency formatting per locale
- RTL support for Arabic/Hebrew (future phase)

---

## White-Label & Reseller Program

**Status:** Phase 4

Agencies and IT consultancies resell FlowFlex under their own brand:

- Custom domain (e.g. `app.agencyname.com`)
- Logo, favicon, primary colour override
- Custom email sender domain
- Reseller billing (reseller pays FlowFlex at wholesale, charges their clients at retail)
- Reseller admin panel: manage all client tenants, billing, usage
- White-label removes all FlowFlex branding from UI

**Why critical:** Massive GTM channel. One agency with 50 clients = 50 new tenants without FlowFlex marketing spend.

---

## Mobile Apps (iOS & Android)

**Status:** Phase 5

- Core functionality: approvals, notifications, time tracking, leave requests, task management
- Push notifications via FCM (Android) + APNs (iOS)
- Offline-capable for field workers (Operations: field jobs, inspections)
- Built with React Native or Capacitor (TBD) — shares Vue components where possible
- Biometric authentication (Face ID, fingerprint)

---

## Multi-Entity (Subsidiaries)

**Status:** Phase 6 — covered by [[multi-entity-consolidation]] in Finance domain

Single account manages multiple legal entities. Each entity = separate `company` record with shared auth under a parent account. Finance consolidation module handles cross-entity reporting.

---

## API Developer Portal

**Status:** Phase 5

- Public REST API documentation (auto-generated from routes + OpenAPI spec)
- Interactive API explorer (Swagger UI)
- OAuth 2.0 + API key auth
- SDKs: PHP, JavaScript, Python (auto-generated)
- Sandbox environment (separate tenant with test data)
- Webhook documentation + event catalogue
- Rate limit documentation per plan tier

---

## App Marketplace (FlowFlex Apps)

**Status:** Phase 8

Third-party partners build modules/integrations:
- FlowFlex Apps SDK for building extensions
- Marketplace listing with install/uninstall
- Revenue sharing (70/30 or 80/20)
- Approved connectors for popular tools
- Custom field types, custom views, custom automations via SDK

---

## In-App NPS & Product Analytics (for FlowFlex itself)

**Status:** Phase 3 (internal use)

FlowFlex runs its own PLG tooling on itself:
- In-app NPS survey (triggered at 30/90/180 day mark)
- Feature usage tracking (which modules are actually used)
- Churn risk signals (company hasn't logged in for 14 days)
- Upgrade prompt engine (company near plan limits)

Uses the [[MOC_PLG]] domain internally.

---

## SOC 2 / Trust & Compliance

**Status:** Phase 4

- Live trust page (`trust.flowflex.app`) showing real-time compliance status
- SOC 2 Type II certification
- ISO 27001 certification
- GDPR DPA (Data Processing Agreement) self-serve download
- EU data residency option (Frankfurt datacenter)
- HIPAA-ready tier (future healthcare ICP)

---

## Related

- [[MOC_Concepts]]
- [[multi-tenancy]]
- [[auth-rbac]]
- [[MOC_PLG]] — PLG domain provides these tools for FlowFlex's own customers
