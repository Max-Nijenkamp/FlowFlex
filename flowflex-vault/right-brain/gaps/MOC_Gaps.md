---
type: moc
section: right-brain/gaps
color: "#F97316"
last_updated: 2026-05-13 (Service type fixes + model defaults — 3 more gaps resolved)
---

# Gaps — Missing Features & Tech Debt

Discovered during the build. Links the real work back to the spec.

---

## Open Gaps

| ID | Gap | Severity | Category | Module | Discovered | File |
|---|---|---|---|---|---|---|
| GAP-016 | Sandbox — no provisioning logic, no clone/reset, no subdomain routing | medium | feature | sandbox-environment | 2026-05-10 | [[gap_sandbox-no-provisioning]] |

## Resolved Gaps (Service Type Fixes + Model Defaults 2026-05-13)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-051 ✅ | 55+ service files import `Illuminate\Support\Collection` but contracts declare `Illuminate\Database\Eloquent\Collection` — PHP FatalError on class load, tests silently exit code 2 | high | Bulk `perl -pi` replace across all `app/Services/` files; `Eloquent\Collection` extends `Support\Collection` so covariant return types are valid | 2026-05-13 |
| GAP-052 ✅ | `ProductReview`, `Shipment`, `AbandonedCart` models missing PHP-level `$attributes` defaults — DB DEFAULT values not reflected in Eloquent model after `create()`, status is null | low | Added `protected $attributes` with status defaults to all 3 models | 2026-05-13 |
| GAP-053 ✅ | `ProductRecommendationService`: filters by `status = 'published'` (invalid enum — valid: draft/active/archived) and filters `ecommerce_order_items` by `company_id` (column doesn't exist — must join `ecommerce_orders`) | medium | Changed status filter to `'active'`; `getBestsellers()` now joins `ecommerce_orders` to access `company_id` | 2026-05-13 |

## Resolved Gaps (Phase 6-8 Test Stabilization 2026-05-12)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-046 ✅ | `fake()->ulid()` used in 77 test locations — FakerPHP has no ulid format | medium | Global sed: `fake()->ulid()` → `(string) \Illuminate\Support\Str::ulid()` across 20 test files | 2026-05-12 |
| GAP-047 ✅ | Bulk `insert()` bypasses HasUlids — `id` NULL violation on CommunityMetric, PointTransaction, CashFlowForecast | medium | Added explicit `'id' => (string) \Illuminate\Support\Str::ulid()` to each row in 3 test insert arrays | 2026-05-12 |
| GAP-048 ✅ | `getOriginal('sources')` returns cast array not raw JSON — `json_decode(array)` TypeError in CustomerDataService | low | Changed to `getRawOriginal('sources')` which bypasses Eloquent cast; `forceDelete()` for merge operation | 2026-05-12 |
| GAP-049 ✅ | Boolean/integer columns without `$attributes` default return null on create (SalesCoachingInsight.is_actioned, SalesSequence.step_count, ClientPortalConfig.is_active) | low | Added `protected $attributes = [...]` defaults to all 3 models | 2026-05-12 |
| GAP-050 ✅ | `withoutGlobalScopes()` removes SoftDeletes scope — soft-deleted records still found in test assertions | low | Changed `withoutGlobalScopes()->find()` to `find()` in CollaborationServiceTest + ResourceAllocationServiceTest | 2026-05-12 |

## Resolved Gaps (Phase 0-5 Final Closure 2026-05-12)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-040 ✅ | Vite config missing 8 Phase 3-5 panel CSS entries — HTTP 500 on all Phase 3-5 panel pages | high | Added finance/crm/operations/it/legal/ecommerce/marketing/comms theme.css entries to vite.config.js; rebuilt — all 12 panels now in manifest | 2026-05-12 |
| GAP-041 ✅ | projects.documents.* permissions missing from PermissionSeeder (5 permissions) | medium | Added view-any/view/create/edit/delete; total 171→176; test assertions updated | 2026-05-12 |
| GAP-042 ✅ | LocalCompanySeeder not activating projects.gantt, projects.documents, projects.templates | medium | All 3 keys added to LocalCompanySeeder | 2026-05-12 |
| GAP-043 ✅ | TimeEntryService::calculateHours() implicit nullable params — PHP 8.4 deprecation | low | Changed `string $x = null` → `?string $x = null` on 3 params | 2026-05-12 |
| GAP-044 ✅ | DocumentService missing — DocumentResource + Document model had no service layer | medium | Built DocumentServiceInterface + DocumentService + DocumentFactory + 3 tests; bound in ProjectsServiceProvider | 2026-05-12 |
| GAP-045 ✅ | HrAnalyticsService missing — HR widgets queried models without a service layer | low | Built HrAnalyticsServiceInterface + HrAnalyticsService + 4 tests; bound in HrServiceProvider | 2026-05-12 |

## Resolved Gaps (Phase 0-5 Audit 2026-05-11)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-029 ✅ | STATUS_Dashboard showed 0% for all Phase 3-5 domains — massively stale | medium | Updated with accurate counts (Finance 35%, CRM 23%, Ops 22%, IT 33%, Legal 50%, Ecommerce 20%, Marketing 21%, Comms 36%) | 2026-05-11 |
| GAP-030 ✅ | Operations module keys mismatch: catalog used `ops.*` but all Filament resources used `operations.*` — entire Operations panel unreachable | critical | ModuleCatalogSeeder updated: ops.inventory→operations.inventory, ops.purchasing→operations.procurement, ops.warehousing→operations.warehousing, ops.logistics→operations.logistics | 2026-05-11 |
| GAP-031 ✅ | 11 module keys missing from ModuleCatalogSeeder: crm.tickets, it.change-mgmt, it.saas-spend, legal.risks, legal.privacy, ecommerce.products, comms.booking, comms.intranet, comms.knowledge, marketing.events | high | All 11 added to ModuleCatalogSeeder | 2026-05-11 |
| GAP-032 ✅ | Zero Phase 3-5 permissions in PermissionSeeder — all Phase 3-5 panels returned 403 | critical | 102 new permissions added (Finance 22, CRM 18, Ops 11, IT 11, Legal 11, Ecommerce 7, Marketing 12, Comms 10). Total: 69→171 | 2026-05-11 |
| GAP-033 ✅ | IT, Legal, Ecommerce, Marketing had Filament panels but no service providers — services couldn't be injected | high | Created ItServiceProvider, LegalServiceProvider, EcommerceServiceProvider, MarketingServiceProvider; registered in bootstrap/providers.php | 2026-05-11 |
| GAP-034 ✅ | No domain services for IT, Legal, Ecommerce, Marketing (only models + resources existed) | high | Built: ItTicketService, ItAssetService, LegalContractService, LegalPolicyService, EcommerceOrderService, EcommerceProductService, EmailCampaignService, CmsPageService. 12 new tests. | 2026-05-11 |
| GAP-035 ✅ | LocalCompanySeeder only activated HR + Projects modules — demo company couldn't access Phase 3-5 panels | medium | Added 28 Phase 3-5 module activations to LocalCompanySeeder | 2026-05-11 |
| GAP-036 ✅ | LocalDemoDataSeeder had no Phase 3-5 data — panels loaded but empty | medium | Added 8 new seed methods: seedFinance, seedCrm, seedOperations, seedIt, seedLegal, seedEcommerce, seedMarketing, seedComms | 2026-05-11 |
| GAP-037 ✅ | Project Templates module missing (Phase 2 — final missing Phase 2 module) | medium | Built: ProjectTemplate model + migration + factory + service + Filament resource + 3 tests | 2026-05-11 |
| GAP-038 ✅ | Project Budget & Costs module missing (Phase 3) | medium | Built: ProjectBudget model + migration + factory + service + Filament resource + 3 tests | 2026-05-11 |
| GAP-039 ✅ | PHP memory limit 128MB in Docker container — full test suite OOM-crashed | low | Added `memory_limit = 512M` to /usr/local/etc/php/conf.d/memory.ini in Dockerfile | 2026-05-11 |

## Resolved Gaps (Security Hardening 2026-05-11)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-020 ✅ | Laravel Sanctum not installed — API auth:sanctum guard completely broken | critical | Installed laravel/sanctum v4.3, ulidMorphs migration, HasApiTokens on User, 5 API auth tests | 2026-05-11 |
| GAP-021 ✅ | API routes missing SetCompanyContext — no company scoping on REST API | critical | SetCompanyContext added to `auth:sanctum` route group in api.php | 2026-05-11 |
| GAP-022 ✅ | CRM dropdowns (CrmTicket owner, CrmDeal owner) leaked all-company user emails | critical | Added `->where('company_id', $companyId)` to withoutGlobalScopes() queries | 2026-05-11 |
| GAP-023 ✅ | Stripe webhook returned 200 when webhook secret not configured | high | Controller now hard-fails with 500 when no secret configured | 2026-05-11 |
| GAP-024 ✅ | Auth endpoint had no rate limiting | high | 5 req/min per email+IP via RateLimiter::for('auth') | 2026-05-11 |
| GAP-025 ✅ | TaskService::reorder() performed bulk UPDATE without ownership check | high | Validates all task IDs belong to current company before UPDATE | 2026-05-11 |
| GAP-026 ✅ | AdminUserResource accessible to all admin roles, not just super_admin | high | Added canAccess() checking role === 'super_admin' | 2026-05-11 |
| GAP-027 ✅ | LeaveRequestFactory missing policy_id (NOT NULL FK) | medium | Added LeavePolicy::factory() to LeaveRequestFactory definition | 2026-05-11 |
| GAP-028 ✅ | EmployeeController::store() missing employee_number auto-generation | medium | Auto-generates EMP-XXXX when not provided; defaults hire_date to today | 2026-05-11 |
| GAP-010 ✅ | Core Platform Phase 1 — DataImport + Sandbox UI missing | medium | DataImportPage + SandboxPage built in gap closure session 2026-05-11 | 2026-05-11 |
| GAP-015 ✅ | DataImportEngine — no Filament UI | medium | DataImportPage (CSV upload → parse → import) built | 2026-05-11 |

## Resolved Gaps (Phase 0–2 security audit 2026-05-11)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-019 ✅ | Projects resources + EmployeeResource: 10 unscoped dropdown queries leak cross-tenant data | high | All 10 `Model::query()->pluck()` calls replaced with `withoutGlobalScopes()->where('company_id',...)` pattern. 7 files fixed. | 2026-05-11 |

## Resolved Gaps (Phase 2 Projects build 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-018 ✅ | BelongsToMany pivot table with ULID id column fails on insert — Eloquent never populates it | medium | Removed id column from sprint_tasks; composite PK ['sprint_id','task_id'] used. ADR logged. | 2026-05-10 |

## Resolved Gaps (Phase 2 HR build 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-017 ✅ | PostgreSQL self-referential FK fails inside Schema::create — no unique constraint error | medium | Moved FK to separate Schema::table block after create; pattern documented as ADR | 2026-05-10 |

## Resolved Gaps (Phase 1 audit 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-011 ✅ | BelongsToCompany missing on BillingSubscription, Sandbox, SetupWizardProgress — data leak | high | Trait added to all 3 models; BillingService methods use withoutGlobalScopes() | 2026-05-10 |
| GAP-012 ✅ | NotificationLog model used table `notification_logs` but migration created `notification_log` | high | `protected $table = 'notification_log'` added | 2026-05-10 |
| GAP-013 ✅ | DataImportService row numbers reset to 1 for every chunk of 100 | medium | Running $offset counter fixed sequential numbering across chunks | 2026-05-10 |
| GAP-014 ✅ | ActivityLogResource allowed edit/delete on immutable audit records | high | canEdit(), canDelete(), canDeleteAny() all return false | 2026-05-10 |
| GAP-002 (fixed) | Company scope not applied in Filament panel — data leak | critical | architecture | testing-standards | 2026-05-09 | [[gap_company-scope-filament-middleware]] |
| GAP-006 (fixed) | Missing tests for CompanyCreationService, ModuleMarketplace, CompanySettings | medium | spec | admin-panel-flowflex | 2026-05-09 | [[gap_missing-critical-path-tests]] |

---

## Gap Types

| Tag | Meaning |
|---|---|
| `#gap/spec` | Something is missing or wrong in the Left Brain spec |
| `#gap/feature` | A user-facing feature that should be in a module but isn't |
| `#gap/architecture` | An architectural issue — pattern, performance, security |
| `#gap/bug` | A defect found during build or review |
| `#gap/tech-debt` | Something built quickly that needs a proper solution |

---

## Gap Template

When you discover a gap during a build session:

```markdown
---
type: gap
tag: gap/feature
module: {{module}}
domain: {{domain}}
discovered: YYYY-MM-DD
status: open | in-progress | resolved
---

# Gap: {{short title}}

## Context
Where in the build was this found? What were you trying to do?

## The Problem
What is missing or broken?

## Impact
Who is affected? What breaks if not fixed?

## Proposed Solution
How should this be fixed?

## Links
- Source builder log: [[builder-logs/module-name]]
- Related spec: [[left-brain/domains/.../module-name]]
```

---

## Resolved Gaps

| ID | Gap | Resolution | Date |
|---|---|---|---|
| GAP-001 | Phase placement corrections (ATS, Sales Sequences, Bank Feeds, Partner Mgmt) | Specs updated: Sales Sequences → Phase 3, Open Banking → Phase 3; ATS already Phase 4 | 2026-05-09 |
| GAP-007 | ModuleMarketplace + CompanySettings: no authorization check | `canAccess()` + `abort_unless(canManageModules(), 403)` added; blade hides buttons for non-owners; 2 new tests | 2026-05-09 |
| GAP-008 | RoleResource: no delete action for custom tenant roles | `DeleteAction` added; protected: `owner` role hidden from edit/delete; blocks delete if role has assigned users | 2026-05-09 |
| GAP-009 | platform_announcements: missing indexes on sent_at, target, created_by | Migration 000013 adds all 3 indexes | 2026-05-09 |
| GAP-003 | CompanyContext singleton leaks across Horizon worker jobs | `WithCompanyContext` job middleware created — sets + clears context + permissions team in `finally` block | 2026-05-09 |
| GAP-004 | Invite tokens stored only in Redis cache — cache flush = permanent lockout | `user_invitations` table created (migration 000010); `CompanyCreationService` now persists to DB | 2026-05-09 |
| GAP-005 | PlatformAnnouncement "Send" action is a stub — dispatches nothing | `PlatformAnnouncementSent` event + `DispatchAnnouncementJob` + `PlatformAnnouncementNotification` created; resource wired up | 2026-05-09 |

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
