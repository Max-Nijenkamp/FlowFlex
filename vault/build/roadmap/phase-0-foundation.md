---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-04
---

# Phase 0 — Foundation

Runnable skeleton: Laravel + docker + tenancy + queues + email + panels + permission seed + test suite. Nothing user-visible yet beyond login.

**8 modules · 17 features — ✅ ALL TICKED 2026-07-04** after the reconciliation sweep: every spec Test Checklist item mapped to a green Pest test or an explicit live gate (annotated inline). Sweep additions: mail suppression list implemented + tested, `horizon:snapshot` scheduled with mutex flags, `Http::preventStrayRequests()` harness guard, webhook-throttle / login-validation / login-throttle / horizon-priority / schedule-flags / wizard-no-op tests (`FoundationGapsTest`, suite 50 tests green).

## foundation

### Laravel Scaffold — `foundation.laravel-scaffold`

Build: `/flowflex:start foundation.laravel-scaffold` · Done: `/flowflex:done foundation.laravel-scaffold` · Spec: [[../../domains/foundation/laravel-scaffold/_module|hub]] · Hard deps: none

- [x] **Soft-Delete Lifecycle** ([[../../domains/foundation/laravel-scaffold/features/soft-delete-lifecycle|spec]]) — `SoftDeleteLifecycleTest` + `ModelsTest` arch gate
- [x] **ULID Identity Strategy** ([[../../domains/foundation/laravel-scaffold/features/ulid-identity|spec]]) — `UlidIdentityTest` + `ModelsTest` + foreignUlid migration assert
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Docker Environment — `foundation.docker-environment`

Build: `/flowflex:start foundation.docker-environment` · Done: `/flowflex:done foundation.docker-environment` · Spec: [[../../domains/foundation/docker-environment/_module|hub]] · Hard deps: foundation.scaffold

- [x] **Local Dev Stack (`docker compose up`)** ([[../../domains/foundation/docker-environment/features/dev-stack|spec]]) — live gate: 9 services healthy via `docker compose ps`, :8080 serves, container `migrate:fresh --seed` clean (compose file lives outside the app container — not Pest-testable; `SeederTest` covers the seed half)
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Multi-Tenancy Layer — `foundation.multi-tenancy-layer`

Build: `/flowflex:start foundation.multi-tenancy-layer` · Done: `/flowflex:done foundation.multi-tenancy-layer` · Spec: [[../../domains/foundation/multi-tenancy-layer/_module|hub]] · Hard deps: foundation.scaffold

- [x] **Persistent Request Context (`SetCompanyContext`, Livewire-safe)** ([[../../domains/foundation/multi-tenancy-layer/features/persistent-context|spec]]) — `PanelAuthTest` + `TenantIsolationTest`; Livewire follow-up POST = `/flowflex:verify` probe (live gate)
- [x] **Query Auto-Scoping (`CompanyScope` + `BelongsToCompany`)** ([[../../domains/foundation/multi-tenancy-layer/features/query-auto-scoping|spec]]) — `TenantIsolationTest` (4 asserts) + `TenancyTest` arch gate
- [x] **Queue Tenant-Context Propagation (`WithCompanyContext`)** ([[../../domains/foundation/multi-tenancy-layer/features/queue-context|spec]]) — `QueueContextTest` incl. null-tenant guard
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Filament Panels — `foundation.filament-panels`

Build: `/flowflex:start foundation.filament-panels` · Done: `/flowflex:done foundation.filament-panels` · Spec: [[../../domains/foundation/filament-panels/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [x] **Admin Panel Shell (`/admin`)** ([[../../domains/foundation/filament-panels/features/admin-panel-shell|spec]]) — `PanelAuthTest` cross-guard + shell render; hand-checked repeatedly
- [x] **App Panel Shell (`/app`)** ([[../../domains/foundation/filament-panels/features/app-panel-shell|spec]]) — `PanelAuthTest` (context per request, 402 suspended) + wizard-no-op in `FoundationGapsTest` (full redirect lands with core.setup-wizard); Livewire `$refresh` = `/flowflex:verify` probe. 2026-07-04 chrome rework: ADR [[../../decisions/decision-2026-07-04-panel-chrome-ownership|panel-chrome-ownership]]
- [x] **Panel Auth (login, 2FA, profile — shared `Filament\Auth`)** ([[../../domains/foundation/filament-panels/features/panel-auth|spec]]) — `PanelAuthTest` + `LoginRedirectTest` + `FoundationGapsTest` (invalid-credential error, throttle notification); 2FA setup + sectioned profile hand-checked 2026-07-04
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Queue Workers & Scheduler — `foundation.queue-workers`

Build: `/flowflex:start foundation.queue-workers` · Done: `/flowflex:done foundation.queue-workers` · Spec: [[../../domains/foundation/queue-workers/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [x] **Job Processing (Horizon + prioritised Redis queues)** ([[../../domains/foundation/queue-workers/features/job-processing|spec]]) — `HorizonGateTest` + supervisor priority-order assert (`FoundationGapsTest`); failed-job-retry = live gate on the Horizon dashboard
- [x] **Scheduled Commands (`scheduler` service)** ([[../../domains/foundation/queue-workers/features/scheduled-commands|spec]]) — `horizon:snapshot` scheduled 2026-07-04; every event asserts `withoutOverlapping` + `onOneServer` (`FoundationGapsTest`); no tenant-scoped scheduled work exists yet
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Test Suite — `foundation.test-suite`

Build: `/flowflex:start foundation.test-suite` · Done: `/flowflex:done foundation.test-suite` · Spec: [[../../domains/foundation/test-suite/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [x] **Architecture Tests (isolation & layering enforcement)** ([[../../domains/foundation/test-suite/features/architecture-tests|spec]]) — `LayersTest` + `ModelsTest` + `TenancyTest`
- [x] **Tenant-Aware Test Harness (`setCompany` + RefreshDatabase)** ([[../../domains/foundation/test-suite/features/tenant-aware-harness|spec]]) — `setCompany()` in Pest.php; `Http::preventStrayRequests()` added 2026-07-04; rate limiter isolates per test via the array cache store
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Email Setup — `foundation.email-setup`

Build: `/flowflex:start foundation.email-setup` · Done: `/flowflex:done foundation.email-setup` · Spec: [[../../domains/foundation/email-setup/_module|hub]] · Hard deps: foundation.scaffold, users.email, foundation.queues

- [x] **Bounce Webhook (signature-verified suppression)** ([[../../domains/foundation/email-setup/features/bounce-webhook|spec]]) — `BounceWebhookTest` + route middleware assert (signature + `throttle:60,1`, `FoundationGapsTest`)
- [x] **Branded, Queued Mailable (`FlowFlexMailable`)** ([[../../domains/foundation/email-setup/features/branded-mailable|spec]]) — `MailBrandingTest`; suppression-list skip **implemented 2026-07-04** (`send()` filters `email_deliverable = false`) + tested both ways
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Permissions Seeder — `foundation.permissions-seed`

Build: `/flowflex:start foundation.permissions-seed` · Done: `/flowflex:done foundation.permissions-seed` · Spec: [[../../domains/foundation/permissions-seed/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy, foundation.panels

- [x] **Demo-Data Seeding (`LocalDevSeeder`, non-prod)** ([[../../domains/foundation/permissions-seed/features/demo-data-seeding|spec]]) — `SeederTest` (prod refusal, demo logins, owner grants)
- [x] **Permission & Module-Catalog Seeding** ([[../../domains/foundation/permissions-seed/features/permission-seeding|spec]]) — `SeederTest` idempotency + team-scoped grants; ⚠ ModuleCatalogSeeder self-skips until the `module_catalog` table lands with **core.billing-engine** (deferred, not a gap)
- [x] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
