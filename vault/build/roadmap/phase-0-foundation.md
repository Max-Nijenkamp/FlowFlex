---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 0 — Foundation

Runnable skeleton: Laravel + docker + tenancy + queues + email + panels + permission seed + test suite. Nothing user-visible yet beyond login.

**8 modules · 17 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## foundation

### Laravel Scaffold — `foundation.laravel-scaffold`

Build: `/flowflex:start foundation.laravel-scaffold` · Done: `/flowflex:done foundation.laravel-scaffold` · Spec: [[../../domains/foundation/laravel-scaffold/_module|hub]] · Hard deps: none

- [ ] **Soft-Delete Lifecycle** ([[../../domains/foundation/laravel-scaffold/features/soft-delete-lifecycle|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **ULID Identity Strategy** ([[../../domains/foundation/laravel-scaffold/features/ulid-identity|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Docker Environment — `foundation.docker-environment`

Build: `/flowflex:start foundation.docker-environment` · Done: `/flowflex:done foundation.docker-environment` · Spec: [[../../domains/foundation/docker-environment/_module|hub]] · Hard deps: foundation.scaffold

- [ ] **Local Dev Stack (`docker compose up`)** ([[../../domains/foundation/docker-environment/features/dev-stack|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Multi-Tenancy Layer — `foundation.multi-tenancy-layer`

Build: `/flowflex:start foundation.multi-tenancy-layer` · Done: `/flowflex:done foundation.multi-tenancy-layer` · Spec: [[../../domains/foundation/multi-tenancy-layer/_module|hub]] · Hard deps: foundation.scaffold

- [ ] **Persistent Request Context (`SetCompanyContext`, Livewire-safe)** ([[../../domains/foundation/multi-tenancy-layer/features/persistent-context|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Query Auto-Scoping (`CompanyScope` + `BelongsToCompany`)** ([[../../domains/foundation/multi-tenancy-layer/features/query-auto-scoping|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Queue Tenant-Context Propagation (`WithCompanyContext`)** ([[../../domains/foundation/multi-tenancy-layer/features/queue-context|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Filament Panels — `foundation.filament-panels`

Build: `/flowflex:start foundation.filament-panels` · Done: `/flowflex:done foundation.filament-panels` · Spec: [[../../domains/foundation/filament-panels/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [ ] **Admin Panel Shell (`/admin`)** ([[../../domains/foundation/filament-panels/features/admin-panel-shell|spec]]) — hand-check: open `/admin` — staff nav + content.; manage companies, view billing, reach `/horizon`.
- [ ] **App Panel Shell (`/app`)** ([[../../domains/foundation/filament-panels/features/app-panel-shell|spec]]) — hand-check: open `/app` — sidebar nav + topbar + content region.; navigate resources; global search/spotlight; notification bell (polling).
- [ ] **Panel Auth (login, 2FA, profile — shared `Filament\Auth`)** ([[../../domains/foundation/filament-panels/features/panel-auth|spec]]) — hand-check: open `/app/login`, `/app/password-reset`, `/app/verify-email`, `/app/profile` (+ `/admin` equivalents).; submit credentials → (2FA challenge if enabled) → panel; reset flow via emailed link; toggle 2FA in profile.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Queue Workers & Scheduler — `foundation.queue-workers`

Build: `/flowflex:start foundation.queue-workers` · Done: `/flowflex:done foundation.queue-workers` · Spec: [[../../domains/foundation/queue-workers/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [ ] **Job Processing (Horizon + prioritised Redis queues)** ([[../../domains/foundation/queue-workers/features/job-processing|spec]]) — hand-check: open Horizon dashboard (`/horizon`) — throughput, failed jobs, metrics.
- [ ] **Scheduled Commands (`scheduler` service)** ([[../../domains/foundation/queue-workers/features/scheduled-commands|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Test Suite — `foundation.test-suite`

Build: `/flowflex:start foundation.test-suite` · Done: `/flowflex:done foundation.test-suite` · Spec: [[../../domains/foundation/test-suite/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy

- [ ] **Architecture Tests (isolation & layering enforcement)** ([[../../domains/foundation/test-suite/features/architecture-tests|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Tenant-Aware Test Harness (`setCompany` + RefreshDatabase)** ([[../../domains/foundation/test-suite/features/tenant-aware-harness|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Email Setup — `foundation.email-setup`

Build: `/flowflex:start foundation.email-setup` · Done: `/flowflex:done foundation.email-setup` · Spec: [[../../domains/foundation/email-setup/_module|hub]] · Hard deps: foundation.scaffold, users.email, foundation.queues

- [ ] **Bounce Webhook (signature-verified suppression)** ([[../../domains/foundation/email-setup/features/bounce-webhook|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Branded, Queued Mailable (`FlowFlexMailable`)** ([[../../domains/foundation/email-setup/features/branded-mailable|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Permissions Seeder — `foundation.permissions-seed`

Build: `/flowflex:start foundation.permissions-seed` · Done: `/flowflex:done foundation.permissions-seed` · Spec: [[../../domains/foundation/permissions-seed/_module|hub]] · Hard deps: foundation.scaffold, foundation.tenancy, foundation.panels

- [ ] **Demo-Data Seeding (`LocalDevSeeder`, non-prod)** ([[../../domains/foundation/permissions-seed/features/demo-data-seeding|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Permission & Module-Catalog Seeding** ([[../../domains/foundation/permissions-seed/features/permission-seeding|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
