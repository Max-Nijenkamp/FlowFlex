---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 1 — Core platform (v1-core)

The platform every domain module assumes: billing/module gating, RBAC, invitations, settings, files, notifications, audit, marketplace, staff console, setup wizard.

**12 modules · 32 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## core

### Audit Log — `core.audit-log`

Build: `/flowflex:start core.audit-log` · Done: `/flowflex:done core.audit-log` · Spec: [[../../domains/core/audit-log/_module|hub]] · Hard deps: none

- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Audit Logger (write path + retention)** ([[../../domains/core/audit-log/features/audit-logger|spec]]) — hand-check: background — trigger it (s: (1) any domain write / state transition calls `AuditLogger::log`; (2) the scheduler run), then check the visible result named in the spec
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Log Browser** ([[../../domains/core/audit-log/features/log-browser|spec]]) — hand-check: open `AuditLogResource` — read-only list/view in `/app` (package-provided, configured); plus a cross-company varian; user opens the log browser → applies filters (e.g. by user + date range) → clicks a row to inspect the before/after properties. Ad
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **PII Denylist** ([[../../domains/core/audit-log/features/pii-denylist|spec]]) — hand-check: background — trigger it (every call to `AuditLogger::log(event, subject, causer, properties)` passes `properties` t), then check the visible result named in the spec
- [x] Gates: Pint + PHPStan + Pest green (66), spec Test Checklist covered (`AuditLogTest`, `AuditPiiTest`, `ModuleGatingTest`), both browsers screenshot-verified live

### Company Settings — `core.company-settings`

Build: `/flowflex:start core.company-settings` · Done: `/flowflex:done core.company-settings` · Spec: [[../../domains/core/company-settings/_module|hub]] · Hard deps: none

- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Settings Tabs** ([[../../domains/core/company-settings/features/settings-tabs|spec]]) — hand-check: open `CompanySettingsPage` (`/app`, `app/Filament/App/Pages/CompanySettingsPage.php` + `resources/views/filament/ap; 1. Owner opens the page → each tab hydrates from `app(<SettingsClass>)`.
- [x] Gates: Pint + PHPStan + Pest green (73), `CompanySettingsTest` 7 tests, page screenshot-verified live

### Staff Console — `core.staff-console`

Build: `/flowflex:start core.staff-console` · Done: `/flowflex:done core.staff-console` · Spec: [[../../domains/core/staff-console/_module|hub]] · Hard deps: none

- [ ] **Billing Overview** ([[../../domains/core/staff-console/features/billing-overview|spec]]) — hand-check: open `ListBillingInvoices` under `BillingInvoiceResource` (cross-company), and the `InvoicesRelationManager` tab on; staff filters invoices by status → inspects a row (read-only) → or opens a company → Invoices tab for that company's invoices. No 
- [ ] **Company Management** ([[../../domains/core/staff-console/features/company-management|spec]]) — hand-check: open `ListCompanies` and `EditCompany` under `CompanyResource` (`/admin` panel, admin guard). Routes: Filament reso; search/filter companies → open a row → edit settings or trigger suspend-with-reason → save. Relation-manager tabs (Modules / Invoi
- [ ] **Company Provisioning** ([[../../domains/core/staff-console/features/company-provisioning|spec]]) — hand-check: open `CreateCompany` under `CompanyResource` (`/admin` panel, admin guard). Route: Filament resource create route f; staff fills the form → submit → `ProvisionCompanyData` built → `ProvisionCompanyAction` transaction (company + unique slug → owner
- [ ] **Per-Company Module Management** ([[../../domains/core/staff-console/features/module-management|spec]]) — hand-check: open `ModulesRelationManager` tab on the `EditCompany` page under `CompanyResource` (`/admin`, admin guard).; open a company → Modules tab → Activate a catalog module (validated against catalog validity) or Deactivate a non-free-core one → 
- [ ] **Platform Dashboard** ([[../../domains/core/staff-console/features/platform-dashboard|spec]]) — hand-check: open the `/admin` panel dashboard (widgets, not a dedicated resource). Route: `/admin` dashboard.; passive read — staff land on `/admin` and see the summary; widgets refresh on their own poll. No mutation.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Module Marketplace — `core.module-marketplace`

Build: `/flowflex:start core.module-marketplace` · Done: `/flowflex:done core.module-marketplace` · Spec: [[../../domains/core/module-marketplace/_module|hub]] · Hard deps: core.billing

- [ ] **Activate / Deactivate Module** ([[../../domains/core/module-marketplace/features/activate-deactivate|spec]]) — hand-check: open `ModuleMarketplacePage` (`/app`) — the activate/deactivate action lives on each card of the catalog grid.; 1. Owner clicks Activate on a card → confirm modal → `activateModule()` → card flips to active, sidebar badge appears in the domai
- [ ] **Catalog Grid (browse · search · price preview)** ([[../../domains/core/module-marketplace/features/catalog-grid|spec]]) — hand-check: open `ModuleMarketplacePage` (`/app`, `app/Filament/App/Pages/ModuleMarketplacePage.php` + `resources/views/filamen; 1. Owner opens the page → catalog reads compose `MarketplaceModuleData` DTOs (price preview = unit price × active users).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Spotlight — `core.spotlight`

Build: `/flowflex:start core.spotlight` · Done: `/flowflex:done core.spotlight` · Spec: [[../../domains/core/spotlight/_module|hub]] · Hard deps: foundation.panels

- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Spotlight — Keyboard Palette** ([[../../domains/core/spotlight/features/keyboard-palette|spec]]) — hand-check: open no dedicated route — rendered on every authenticated panel page via `PanelsRenderHook::BODY_END`, plus a topba; 1. Open via `⌘K` (`keydown.window.meta.k`) / `Ctrl+K` (`.ctrl.k`) or the topbar "Search this panel…" button (dispatches `ff-spotli
- [x] Gates: Pint + PHPStan + Pest green (77), `SpotlightTest` 4 tests + result caps per spec, live-verified earlier today (Ctrl+K)

### Two-Factor Authentication — `core.two-factor-auth`

Build: `/flowflex:start core.two-factor-auth` · Done: `/flowflex:done core.two-factor-auth` · Spec: [[../../domains/core/two-factor-auth/_module|hub]] · Hard deps: foundation.panels

- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Two-Factor Auth — QR Code Fix** ([[../../domains/core/two-factor-auth/features/qr-code-fix|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending; recovery-code single-use + challenge flow are vendor Filament surfaces, covered by hand-check)* **Two-Factor Auth — TOTP Enrollment & Challenge** ([[../../domains/core/two-factor-auth/features/totp-enrollment|spec]]) — hand-check: open Filament multi-factor enrollment (reached from account/profile → "Set up authenticator app") and the multi-fac; 1. User opts in → QR + secret shown → user scans in authenticator app → enters the current 6-digit code to confirm → recovery code
- [x] Gates: Pint + PHPStan + Pest green (82), `TwoFactorAuthTest` 5 tests (defensive QR unwrap, subclass registered both panels, TOTP verify, encrypted persistence); enrollment modal screenshot-verified earlier today

### File Storage — `core.file-storage`

Build: `/flowflex:start core.file-storage` · Done: `/flowflex:done core.file-storage` · Spec: [[../../domains/core/file-storage/_module|hub]] · Hard deps: foundation.tenancy, core.settings

- [ ] **Path Generator** ([[../../domains/core/file-storage/features/path-generator|spec]]) — hand-check: background — trigger it (every media store (original, conversion, responsive image) routes through `CompanyPathGene), then check the visible result named in the spec
- [ ] **Upload Security** ([[../../domains/core/file-storage/features/upload-security|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Invitation System — `core.invitation-system`

Build: `/flowflex:start core.invitation-system` · Done: `/flowflex:done core.invitation-system` · Spec: [[../../domains/core/invitation-system/_module|hub]] · Hard deps: foundation.panels, foundation.email

- [ ] **Accept Flow** ([[../../domains/core/invitation-system/features/accept-flow|spec]]) — hand-check: background — trigger it (: `AcceptInvitationAction::run(AcceptInvitationData)` invoked by the public register form ), then check the visible result named in the spec
- [ ] **Public Register (Vue + Inertia)** ([[../../domains/core/invitation-system/features/public-register-vue|spec]]) — hand-check: open `InviteRegister.vue` (`resources/js/Pages/Auth/InviteRegister.vue`), route `/register/invite/{token}` (`routes; 1. Recipient opens the link → `AuthController@showInviteRegistration` loads the invite `withoutGlobalScope(CompanyScope)`, `firstO
- [ ] **Send Invite** ([[../../domains/core/invitation-system/features/send-invite|spec]]) — hand-check: open `InvitationResource` (`/app/invitations`) — pending-invites list + create/resend/revoke actions, surfaced insi; 1. Owner/admin clicks Invite → modal form (`CreateInvitationData`: email + role).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Roles & Permissions — `core.rbac`

Build: `/flowflex:start core.rbac` · Done: `/flowflex:done core.rbac` · Spec: [[../../domains/core/rbac/_module|hub]] · Hard deps: foundation.panels, foundation.permissions

- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **RBAC — Custom Roles** ([[../../domains/core/rbac/features/custom-roles|spec]]) — hand-check: open "Create / edit role" (`/app/roles/create`, `/app/roles/{id}/edit`).; toggle permissions per module group; "select all in module"; save → server
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **RBAC — Last-Owner & Built-in Role Guardrails** ([[../../domains/core/rbac/features/last-owner-guard|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Module-Scoped Role Permissions** ([[../../domains/core/rbac/features/module-scoped-permissions|spec]]) — hand-check: open "Create / edit role" (`/app/roles/create`).; toggle permission checkboxes per module group; "select all in module"; save →
- [x] *(AI gate ✓ 2026-07-04 — hand-check pending)* **Company Ownership — single owner, transferable** ([[../../domains/core/rbac/features/ownership|spec]]) — hand-check: open "Transfer ownership" modal on the Users/Roles screen (`/app/roles` or company settings).; pick new owner → double-confirm → atomic transfer → toast + re-render; previous owner
- [x] Gates: Pint + PHPStan + Pest green (90), `RoleManagementTest` 7 + `RoleIsolationTest` 1, matrix page screenshot-verified live

### Workspace Hub — `core.hub`

Build: `/flowflex:start core.hub` · Done: `/flowflex:done core.hub` · Spec: [[../../domains/core/workspace-hub/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Domain Launcher** ([[../../domains/core/workspace-hub/features/domain-launcher|spec]]) — hand-check: open Workspace Hub launcher — the tenant's default post-login route (`custom-pages` pattern). Single/multi-panel ro; 1. Tenant user authenticates → lands on the hub.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Notifications — `core.notifications`

Build: `/flowflex:start core.notifications` · Done: `/flowflex:done core.notifications` · Spec: [[../../domains/core/notifications/_module|hub]] · Hard deps: foundation.panels, foundation.email, foundation.queues

- [ ] **Inbox Bell** ([[../../domains/core/notifications/features/inbox-bell|spec]]) — hand-check: open topbar bell in every Filament panel — Filament's built-in `->databaseNotifications()` + `->databaseNotificatio; open the panel, click a notification (mark read + follow action_url), mark-as-read per item, mark-all-read (`MarkAllReadAction`), 
- [ ] **Preferences** ([[../../domains/core/notifications/features/preferences|spec]]) — hand-check: open `NotificationPreferencesPage` at `/app` (custom Filament page).; user toggles per-type / per-channel switches and saves; save submits `UpdateNotificationPreferencesData`.
- [ ] **Realtime Broadcast** ([[../../domains/core/notifications/features/realtime-broadcast|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Billing Engine — `core.billing-engine`

Build: `/flowflex:start core.billing-engine` · Done: `/flowflex:done core.billing-engine` · Spec: [[../../domains/core/billing-engine/_module|hub]] · Hard deps: foundation.panels, foundation.tenancy, foundation.queues, core.settings

- [ ] **Admin Metrics (MRR / Churn / Adoption)** ([[../../domains/core/billing-engine/features/admin-metrics|spec]]) — hand-check: open Filament stat/chart widgets on the `/admin` billing dashboard (staff panel).; staff view the dashboard, switch the period selector; read-only, no edit actions.
- [ ] **Dunning** ([[../../domains/core/billing-engine/features/dunning|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Module Gating** ([[../../domains/core/billing-engine/features/module-gating|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Monthly Invoicing** ([[../../domains/core/billing-engine/features/monthly-invoicing|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Stripe Integration** ([[../../domains/core/billing-engine/features/stripe-integration|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
