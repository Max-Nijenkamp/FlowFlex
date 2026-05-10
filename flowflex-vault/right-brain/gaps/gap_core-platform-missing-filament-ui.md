---
type: gap
severity: medium
category: feature
status: open
color: "#F97316"
discovered: 2026-05-10
discovered_in: core-platform-phase1
last_updated: 2026-05-10
---

# Gap: Core Platform Phase 1 — Missing Filament UI for most modules

## Context

Phase 1 data layer is complete: 6 new migrations (010001–010006), 10+ models, 4 services, 1 middleware, i18n files. Built during the 2026-05-10 session. 134 tests pass.

## The Problem

Most Phase 1 modules have backend infrastructure (migrations, models, services) but no Filament CRUD UI. Only two UIs were built:
- `ActivityLogResource` (admin panel — read-only table)
- `SetupWizard` page (app panel)

Missing Filament UI:
- **Notification Preferences** — no UI to view/edit per-user channel preferences
- **Data Import Engine** — no import job management UI (upload, map columns, view status, rollback)
- **API Client Manager** — no CRUD for `api_clients`, `api_tokens`, `webhook_endpoints`
- **Sandbox Environment** — no admin UI to provision/reset sandboxes
- **Billing** — no subscription management UI, no invoice list, no plan enforcement UI
- **i18n** — no admin UI to preview locale strings or set company locale defaults

## Impact

Phase 2 domains can still begin (they depend on the data layer, not the UI), but company owners and admins cannot manage these features from the panel. Enterprise onboarding wizard works (SetupWizard). Audit log is queryable. Other modules are headless until UI is added.

## Proposed Solution

Build each missing Filament resource per module. Suggested priority:
1. **Billing** — most critical for paid-plan enforcement (BillingResource in admin)
2. **API Client Manager** — needed for external integrations (ApiClientResource in app panel)
3. **Data Import Engine** — high customer value at onboarding (ImportJobResource in app panel)
4. **Notification Preferences** — user-facing UX (NotificationPreferencesPage in app panel)
5. **Sandbox** — enterprise-only feature (SandboxResource in admin)
6. **i18n** — low priority, can be file-based for now

## Links

- Source builder log: [[core-platform-phase1]]
- Related spec: [[audit-log]], [[data-import-engine]], [[notification-preferences]], [[sandbox-environment]]
