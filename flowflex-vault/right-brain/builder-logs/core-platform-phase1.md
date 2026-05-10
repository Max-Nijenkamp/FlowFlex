---
type: builder-log
module: core-platform-phase1
domain: Core Platform
panel: admin + app
phase: 1
started: 2026-05-10
status: complete
color: "#F97316"
left_brain_source: "[[MOC_CorePlatform]]"
last_updated: 2026-05-10
---

# Builder Log: Core Platform — Phase 1

Left Brain source: [[MOC_CorePlatform]]

---

## Sessions

### Session 2026-05-10 (5) — Phase 1 final fix + test coverage pass

**Goal:** Fix 3 bugs found in completion sprint audit. Write comprehensive tests for all new Phase 1 features. Complete brain sync.

**Bugs fixed:**
- `notification_quiet_hours`: `start_time` / `end_time` were NOT NULL — `NotificationPreferencesPage::saveQuietHours()` crashed with `QueryException` when user clicked Save without entering times. Fix: migration `010012_make_quiet_hours_times_nullable` + null guard in `saveQuietHours()` (deletes row when both null, otherwise `updateOrCreate`)
- `EnforceModuleAccess` middleware: was calling `enforceModuleAccess()` but ignoring the return value — module-gated routes were never actually blocked. Fix: `abort(403)` when `enforceModuleAccess()` returns false
- `PermissionSeeder`: was using `->first()` to find one owner role and sync permissions. With multiple companies, only the first owner role was synced. Fix: `->each(fn($role) => $role->syncPermissions(Permission::all()))` to sync ALL owner roles

**Tests added (27 new → 171 total):**
- `tests/Feature/Seeders/PermissionSeederTest.php` — creates 30 permissions, idempotent, all follow naming pattern, syncs to all owner roles across companies, does not grant to non-owner roles
- `tests/Feature/Foundation/SyncOwnerPermissionsListenerTest.php` — syncs on CompanyCreated, no-op when role missing, does not affect non-owner roles
- `tests/Feature/Core/StripeWebhookTest.php` — all 4 webhook events (payment_succeeded, payment_failed, subscription.updated, subscription.deleted), unknown event type 200, missing subscription no-op, signature rejected when secret configured, skipped when no secret
- `tests/Feature/Core/EnforceModuleAccessTest.php` — blocks non-foundation module without subscription, allows with active subscription + billing, blocks with subscription but billing past_due, foundation modules always pass, no-op when no company context
- `tests/Feature/Foundation/Invite/InviteAcceptanceTest.php` — added `email_verified_at` is set on acceptance
- `tests/Feature/Core/NotificationPreferencesTest.php` — null times allowed in DB, null save doesn't throw QueryException, deleting row when both null
- `tests/Feature/Seeders/LocalSeederTest.php` — owner has all 30 permissions

**ADRs completed in MOC_Evolution.md:**
- `decision-2026-05-10-permission-seeder-pattern`
- `decision-2026-05-10-stripe-webhook-pattern`
- `decision-2026-05-10-module-access-middleware-pattern`

**Final state:** 171 tests pass, 0 failures, 317+ assertions. Phase 1 ready for Phase 2.

---

### Session 2026-05-10 (4) — Phase 1 completion sprint (3 parallel agents)

**Goal:** Fix every remaining gap from the spec-vs-code audit. Finish Phase 1 so Phase 2 can begin.

**Built (infra agent):**
- `database/seeders/PermissionSeeder.php` — 30 idempotent permissions (firstOrCreate), owner role synced
- `DatabaseSeeder` — calls PermissionSeeder first
- `LocalCompanySeeder` — syncs `Permission::all()` to owner role after creation
- `app/Listeners/Foundation/SyncOwnerPermissionsListener.php` — fires on CompanyCreated, syncs owner permissions
- `app/Listeners/Foundation/LogUserActivatedListener.php` — fires on UserActivated, writes audit log entry
- `EventServiceProvider` — wired CompanyCreated and UserActivated listeners
- `CompanyCreationService` — calls `BillingService::ensureStripeCustomer()` outside transaction, wrapped in try/catch
- `app/Http/Middleware/EnforceModuleAccess.php` — checks BillingService per request; alias `module.access`
- `app/Http/Controllers/Billing/StripeWebhookController.php` — handles invoice.payment_succeeded, invoice.payment_failed, customer.subscription.updated, customer.subscription.deleted; signature verification when `STRIPE_WEBHOOK_SECRET` set
- `routes/web.php` — Stripe webhook route (CSRF exempt)
- `InviteController::accept()` — added `email_verified_at => now()`
- `DataImportService::validate()` — fixed empty `[]` column mapping bug (now uses `$job->column_mapping ?? []`)
- `SendInviteMailListener` — `$tries = 3`, `$backoff = [10, 60, 300]`, `$timeout = 30`
- `SetupWizardProgress::steps()` — removed `'done'` from steps array (terminal state, not a step)
- Migrations: 010007 (drop dead attribute_changes column, add user_invitations company_id index), 010008 (SoftDeletes on 7 Phase 1 tables), 010009 (Sandbox: redis_prefix, s3_prefix, subdomain, reset_scheduled_at), 010010 (stripe_customer_id on companies, ends_at on billing_subscriptions)
- SoftDeletes added to: BillingSubscription, BillingInvoice, Sandbox, ApiClient, ApiToken, WebhookEndpoint, NotificationWatch

**Built (Filament agent):**
- `SetLocale::class` added to `->middleware([])` in both WorkspacePanelProvider and AdminPanelProvider
- `ActivityLogResource::getNavigationGroup()` → `'Support'`; `'Support'` nav group added to AdminPanelProvider
- `CompanySettings::canAccess()` → `hasPermissionTo('core.company.settings.manage')` (replaced `hasRole('owner')`)
- `CompanySettings` — Branding section added: FileUpload logo_path, FileUpload favicon_path, ColorPicker primary_color
- Migration 010011: logo_path, favicon_path, primary_color added to companies table
- `ModuleMarketplace::canAccess()` → `hasPermissionTo('core.modules.manage')`
- `UserResource` — `resend_invite` table action (visible for status=invited, regenerates token, fires UserInvited)
- `app/Filament/App/Pages/NotificationPreferencesPage.php` — quiet hours UI, Settings nav group
- `app/Filament/App/Resources/ApiClientResource.php` — full CRUD, auto-generates client_id/secret, shows secret once
- `app/Filament/Admin/Resources/BillingResource.php` — read-only subscription list with status badges
- `app/Filament/Admin/Widgets/AdminStatsWidget.php` — MRR, Active Companies, Failed Jobs, Queue Depth
- `app/Filament/Admin/Widgets/MrrStatsWidget.php` — billing page header
- `Dashboard.php` — registers AdminStatsWidget
- Tests: CompanySettingsTest + ModuleMarketplaceTest updated to seed permissions in beforeEach

**Built (vault agent):**
- 8 new left-brain spec files: setup-wizard.md, module-billing-engine.md, notifications-alerts.md, api-integrations-layer.md, file-storage.md, rbac-management-ui.md, company-workspace-settings.md, i18n-localisation.md
- MOC_CorePlatform.md — all 12 modules now linked, status → 🔄 In Progress

**Decisions made:** see decisions logged below.

**Final state:** 144 tests pass, 0 failures, 251 assertions.

---

### Session 2026-05-10 (3) — Phase 0 + Phase 1 full audit

**Goal:** Find and fix all gaps: missing factories, missing models, bugs, scalability issues, test coverage gaps. Tests: 134 → 144 passed, 0 failures.

**Fixed:**

Factories created (16 new):
- Phase 0: `UserInvitationFactory`, `CompanyFeatureFlagFactory`, `PlatformAnnouncementFactory`
- Phase 1 (`database/factories/Core/`): `ImportJobFactory`, `ImportJobRowFactory`, `ApiClientFactory`, `ApiTokenFactory`, `WebhookEndpointFactory`, `SandboxFactory`, `BillingSubscriptionFactory`, `BillingInvoiceFactory`, `SetupWizardProgressFactory`, `NotificationPreferenceFactory`, `NotificationQuietHoursFactory`, `NotificationLogFactory`, `NotificationWatchFactory`

Models fixed:
- `HasFactory` trait added to all Phase 1 models that lacked it
- `BelongsToCompany` trait added to `BillingSubscription`, `Sandbox`, `SetupWizardProgress` (had `company_id` column but no global scope — data leak bug)
- `protected $table = 'notification_log'` added to `NotificationLog` (table name is singular, model defaulted to plural → runtime `QueryException`)
- Duplicate `company()` method definitions removed from 9 models (trait already provides it)

Service fixes:
- `BillingService::ensureStripeCustomer()` and `isBillingActive()` — added `withoutGlobalScopes()` since company is passed explicitly; new global scope on `BillingSubscription` would have filtered results incorrectly
- `DataImportService::parseAndStoreRows()` — fixed row number bug: each 100-row chunk was re-indexed from 0 so row 101 was numbered 1 again. Fixed with running `$offset` counter across chunks.

Filament resource fixes:
- `ActivityLogResource` — added `canEdit()`, `canDelete()`, `canDeleteAny()` all returning `false`. Previously only `canCreate()` was blocked; audit logs were editable/deletable.

New tests (10 added):
- `tests/Feature/Core/NotificationRouterTest.php` — 6 tests: critical bypasses preferences, default db channel fallback, user-configured channels, quiet-hours suppression, company_id in NotificationLog
- `SetLocaleTest` extended — 4 new tests: unauthenticated + Accept-Language header, unsupported locale fallback, region code parsing (`fr-FR` → `fr`), all 5 supported locales

**Decisions made:** None new — all patterns already documented.

**Problems hit:** All were pre-existing bugs, not introduced this session.

---

### Session 2026-05-10 (2) — Setup Wizard UI redesign

**Goal:** Fix setup wizard styling — original view was unstyled/bare.

**Built:**
- `app/Filament/App/Pages/SetupWizard.php` — added `getStepConfig()` returning icon, label, title, description per step
- `resources/views/filament/app/pages/setup-wizard.blade.php` — full redesign:
  - Step progress bar: numbered circles with `ring-2 ring-offset-2`, connecting lines that turn green as steps complete, labels underneath
  - Gradient header banner per step (`bg-gradient-to-br from-primary-50`) with `x-filament::icon`, "Step X of Y" label, bold title
  - Body: step description + tappable shortcut card (company settings / users / marketplace / branding) with hover states
  - CTA row: "X of Y steps completed" counter + size-lg button (label changes per step: Get started / Continue / Finish setup)
  - Done state: centered success circle icon + "Go to dashboard" button
  - Full dark mode support throughout

**Decisions made:**
- Filament `getStepConfig()` pattern — step metadata (icon, label, title, description) moved to PHP class to keep blade clean. View only handles layout.
- Vite must be rebuilt after any new Tailwind class additions — `@source` glob resolves at build time, not runtime. See [[decision-2026-05-10-vite-rebuild-required]].

**Problems hit:**
- After editing the blade view, new Tailwind classes (gradients, ring utilities, shadow utilities) were not in compiled CSS → page rendered unstyled.
- Fix: `npm run build` inside Docker container (`docker exec flowflex_app bash -c "npm run build"`). Build: 611KB + 627KB themes.
- Root cause: Filament `theme.css` uses `source(none)` + explicit `@source` globs. Globs are evaluated at Vite build time only.

---

### Session 2026-05-10

**Goal:** Build all Phase 1 Core Platform infrastructure — migrations, models, services, middleware, tests. Full data layer before any Phase 2 domain begins.

**Built:**

Migrations:
- `database/migrations/2026_05_10_171102_create_activity_log_table.php` — ULID PK, `nullableUlidMorphs` for subject/causer, `company_id`, `ip_address`, `user_agent`, `created_at` only (immutable). Indexes on `[company_id, created_at]`.
- `database/migrations/2026_05_10_171717_create_media_table.php` — spatie/laravel-medialibrary v11.22.1 published migration
- `database/migrations/010001_create_notification_preferences_table.php` — 4 tables: `notification_preferences`, `notification_quiet_hours`, `notification_log`, `notification_watches`
- `database/migrations/010002_create_setup_wizard_progress_table.php` — `setup_wizard_progress` with JSON `completed_steps`, `current_step`, `completed` boolean
- `database/migrations/010003_create_import_jobs_table.php` — `import_jobs` (pending/mapping/validating/importing/done/failed/rolled_back) + `import_job_rows`
- `database/migrations/010004_create_api_clients_table.php` — `api_clients`, `api_tokens`, `webhook_endpoints`
- `database/migrations/010005_create_sandboxes_table.php` — `sandboxes` with unique `company_id`, `database_name`, `seed_type`
- `database/migrations/010006_create_billing_tables.php` — `billing_subscriptions` (unique `company_id`), `billing_invoices`

Models:
- `app/Models/Foundation/ActivityLog.php` — extends `Spatie\Activitylog\Models\Activity`; `HasUlids`, `$timestamps = false`, `$dates = ['created_at']`
- `app/Models/Core/SetupWizardProgress.php` — `steps()`, `hasStep()`, `completeStep()` helper methods
- `app/Models/Core/ImportJob.php` — BelongsToCompany, HasUlids, SoftDeletes
- `app/Models/Core/ImportJobRow.php` — BelongsTo ImportJob
- `app/Models/Core/ApiClient.php` — `protected $attributes = ['is_active' => true]` (critical: Eloquent doesn't read DB defaults)
- `app/Models/Core/ApiToken.php` — BelongsTo ApiClient
- `app/Models/Core/WebhookEndpoint.php` — BelongsToCompany
- `app/Models/Core/Sandbox.php` — unique company_id scope
- `app/Models/Core/BillingSubscription.php` — BelongsToCompany, unique per company
- `app/Models/Core/BillingInvoice.php` — BelongsTo BillingSubscription

Services:
- `app/Services/Core/AuditLogger.php` — wraps `spatie/laravel-activitylog`; taps `company_id`, `ip_address`, `user_agent` onto every log entry; uses `Spatie\Activitylog\Support\ActivityLogger` (not `Spatie\Activitylog\ActivityLogger`)
- `app/Services/Core/NotificationRouter.php` — routes `NotifiableEvent` to channels; critical priority bypasses quiet hours; logs to `notification_log`
- `app/Services/Core/DataImportService.php` — `createJob()`, `parseAndStoreRows()` (bulk insert chunks), `validate()`, `rollback()`
- `app/Services/Core/BillingService.php` — lazy Stripe client (throws RuntimeException if unconfigured, not on construct); `calculateMonthlyAmount()`, `ensureStripeCustomer()`, `isBillingActive()`, `enforceModuleAccess()`

Contracts:
- `app/Contracts/Core/NotifiableEvent.php` — interface enforcing `eventType(): string`, `priority(): string`, `toNotification(User $user): Notification`

Filament Resources:
- `app/Filament/Admin/Resources/ActivityLogResource.php` — read-only; color-coded event badges; filters by log_name and event; uses `getNavigationGroup()` / `getNavigationIcon()` methods (Filament 5 requirement)

Filament Pages:
- `app/Filament/App/Pages/SetupWizard.php` — `canAccess()` guards against missing company context; `mount()` loads progress; `completeStep()` advances wizard; uses `getView()` method (not static `$view` property — PHP static/non-static conflict)

Middleware:
- `app/Http/Middleware/SetLocale.php` — resolves locale from `auth()->user()->locale`, then `Accept-Language` header, then app default; 5 supported locales: en, nl, de, fr, es

i18n:
- `lang/en/ui.php`, `lang/nl/ui.php`, `lang/de/ui.php`, `lang/fr/ui.php`, `lang/es/ui.php` — baseline UI strings for all 5 locales

Models updated with activity logging:
- `app/Models/User.php` — `LogsActivity` + `getActivitylogOptions()` with `dontLogEmptyChanges()`
- `app/Models/Company.php` — same pattern

Config:
- `config/activitylog.php` — `activity_model` → `\App\Models\Foundation\ActivityLog::class`

Invite flow (Foundation, wired up this session):
- `app/Http/Controllers/Foundation/InviteController.php` — show/accept/expired; validates via `UserInvitation::isPending()`; fires `UserActivated` event; logs in user
- `app/Mail/Foundation/UserInvitedMail.php` — Queueable mailable; passes `acceptUrl` to view
- `app/Listeners/Foundation/SendInviteMailListener.php` — ShouldQueue; handles `UserInvited` event
- `app/Providers/EventServiceProvider.php` — registers `UserInvited → SendInviteMailListener`

Tests (all passing — 134 total, 0 failures):
- `tests/Feature/Foundation/Invite/InviteAcceptanceTest.php` — 8 tests (show, invalid/accepted/expired token, activate user, password validation, expired page)
- `tests/Feature/Core/AuditLogTest.php` — 5 tests
- `tests/Feature/Core/NotificationPreferencesTest.php` — 5 tests
- `tests/Feature/Core/SetupWizardTest.php` — 5 tests
- `tests/Feature/Core/BillingServiceTest.php` — 5 tests
- `tests/Feature/Core/DataImportServiceTest.php` — 4 tests
- `tests/Feature/Core/InviteMailTest.php` — 3 tests
- `tests/Feature/Core/LocaleMiddlewareTest.php` — 2 tests
- `tests/Feature/Core/ApiClientTest.php` — 5 tests

**Decisions made:**
- Activity log is immutable — `$timestamps = false`, `$dates = ['created_at']` on ActivityLog model. No `updated_at` column in migration. Prevents spatie ORM from trying to write timestamps. See [[decision-2026-05-10-activity-log-immutability]].
- `nullableUlidMorphs()` required everywhere ULID PKs are in use — `nullableMorphs()` creates bigint morph IDs incompatible with ULID string PKs. See [[decision-2026-05-10-ulid-morph-pattern]].
- `PreventRequestForgery::class` is the correct CSRF class to exclude in Laravel 11 tests — the web middleware group uses `PreventRequestForgery`, not `VerifyCsrfToken`. See [[decision-2026-05-10-laravel11-csrf-class]].
- Stripe uses lazy init — `private ?StripeClient $stripe = null` with accessor throwing `RuntimeException` if key absent. Prevents test suite failures when `STRIPE_SECRET` is not set.

**Problems hit:**
- `Spatie\Activitylog\ActivityLogger` wrong namespace — correct: `Spatie\Activitylog\Support\ActivityLogger`
- `dontSubmitEmptyLogs()` doesn't exist in activitylog v5.0.0 — correct method: `dontLogEmptyChanges()`
- `nullableMorphs()` created bigint `subject_id` — incompatible with ULID PKs. Fix: dropped and recreated `activity_log` table in both `flowflex` and `flowflex_testing` databases directly via psql.
- Medialibrary duplicate table after rollback — `media` table persisted but migration record was removed. Fix: inserted migration record directly in psql.
- `CompanyContext::get()` method doesn't exist — use `current()`. Pattern: `$ctx->hasCompany() ? $ctx->current() : null`.
- `ApiClient::is_active` returned null after create — Eloquent doesn't read DB defaults. Fix: `protected $attributes = ['is_active' => true]`.
- Filament 5 `$navigationGroup` type error — must use methods (`getNavigationGroup()`, `getNavigationIcon()`) not static properties.
- SetupWizard PHP error — parent `Page::$view` is non-static; child can't redeclare as static. Fix: removed static property, added `getView()` method.
- InviteAcceptanceTest POST 419 — `withoutMiddleware(VerifyCsrfToken::class)` had no effect because Laravel 11 web group uses `PreventRequestForgery::class`, not `VerifyCsrfToken`. Fixed by excluding correct class.
- `Illuminate\Contracts\Event\Dispatcher` wrong — must be `Illuminate\Contracts\Events\Dispatcher` (plural).

---

## Gaps Discovered

- [[gap_core-platform-missing-filament-ui]] — Most Phase 1 modules have migrations/models/services but no Filament CRUD UI. Still needed: NotificationPreferences UI, DataImport UI, ApiClient UI, Sandbox admin UI, Billing UI.

---

## Lessons

- Always use `nullableUlidMorphs()` in any morph migration — `nullableMorphs()` silently creates bigint IDs.
- Filament 5: never use static navigation properties; always use methods.
- Laravel 11: CSRF middleware class is `PreventRequestForgery`, not `VerifyCsrfToken`. Document this in TestCase setup.
- spatie/laravel-activitylog v5: `dontLogEmptyChanges()` not `dontSubmitEmptyLogs()`. `ActivityLogger` is in `Support\` namespace.
- Stripe (and any external service): always lazy-init — constructor must not fail in test env.

---

## Post-Build Checklist

- [x] All migrations run cleanly (`php artisan migrate`)
- [x] All tests pass — 134 passed, 0 failures, 234 assertions
- [x] ActivityLogResource renders in admin panel
- [x] SetupWizard page renders in app panel
- [ ] Full Filament CRUD UI for all Phase 1 modules
- [ ] Permissions registered for all Phase 1 modules
- [ ] Left Brain specs created for modules without specs (setup-wizard, billing, api-client, i18n, media)
- [x] [[STATUS_Dashboard]] updated

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
- [[MOC_CorePlatform]]
