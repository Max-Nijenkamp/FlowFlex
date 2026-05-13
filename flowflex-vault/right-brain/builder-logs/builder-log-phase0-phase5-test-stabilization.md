---
type: builder-log
module: phase0-phase5-test-stabilization
domain: Cross-cutting
panel: all
phase: 0–5
started: 2026-05-11
status: complete
color: "#F97316"
left_brain_source: "n/a — stabilization pass"
last_updated: 2026-05-11 (security hardening + API auth + test expansion + TaskApiTest + Docker memory fix)
---

# Builder Log: Phase 0–5 Test Stabilization & Seeder Repair

Cross-cutting stabilization pass. No new modules built. All existing Phase 0–5 code verified green.

---

## Sessions

### Session 2026-05-11 (Security Hardening + API Auth + Test Expansion)

**Goal:** Fix all 16 security audit findings. Install Sanctum. Expand test coverage to all Phase 3–5 services. Get full suite to 0 failures.

**Built / Fixed:**

#### Security Fixes Applied (16 total)

| Priority | Fix | File(s) |
|---|---|---|
| CRITICAL-1 | Stripe webhook hard-fails (500) when secret not configured | `StripeWebhookController.php` |
| CRITICAL-2 | CRM user dropdowns scoped to company_id (data leak) | `CrmTicketResource.php:81`, `CrmDealResource.php:102` |
| CRITICAL-3 | `SetCompanyContext` added to API authenticated route group | `routes/api.php` |
| HIGH-1 | Auth endpoint rate-limited 5/min per email+IP | `routes/api.php` (RateLimiter) |
| HIGH-2 | `TaskService::reorder()` verifies task ownership before bulk UPDATE | `Services/Projects/TaskService.php` |
| HIGH-3 | `AdminUserResource::canAccess()` restricted to `super_admin` role | `Filament/Admin/Resources/AdminUserResource.php` |
| HIGH-4 | File downloads via Filament signed URLs — no raw controller needed | N/A (verified) |
| HIGH-5 | Throttle added to invite POST (10/min) and Stripe webhook (60/min) | `routes/web.php` |
| MEDIUM-1 | `BelongsToCompany::updating()` hook prevents company_id hijacking | `Support/Traits/BelongsToCompany.php` |
| MEDIUM-2 | Enum validation on EmployeeController + TaskController | `Api/V1/EmployeeController.php`, `TaskController.php` |
| MEDIUM-3 | `DataImportPage::canAccess()` gates on `core.import.create` | `Filament/App/Pages/DataImportPage.php` |
| MEDIUM-4 | `WebhookEndpointResource::canAccess()` gates on `core.api.manage-webhooks` | `Filament/App/Resources/WebhookEndpointResource.php` |
| MEDIUM-5 | `SandboxPage::canAccess()` gates on sandbox permissions | `Filament/App/Pages/SandboxPage.php` |
| LOW-1 | AuthController uses `withoutGlobalScopes()` for user lookup | `Api/V1/AuthController.php` |
| LOW-2 | AdminUserResource password hashed with `Hash::make()` | `Filament/Admin/Resources/AdminUserResource.php` |
| LOW-3 | ActivityLog `$guarded` fixed to `public` (matches Spatie parent) | `Models/Foundation/ActivityLog.php` |

#### Sanctum Installation (API Auth was completely broken)

- `auth:sanctum` guard was configured in `config/auth.php` but Sanctum package not installed
- Installed `laravel/sanctum` via composer
- Published Sanctum migrations with `ulidMorphs` instead of `morphs` (ULID PKs are strings, not bigint)
- Added `HasApiTokens` trait to `App\Models\User`
- Result: `createToken()` now works, API token issue/revoke fully functional

#### API Auth token flow: `post /api/v1/auth/token` → issue Sanctum token → use `Authorization: Bearer {token}` on protected routes

#### Bug Fixes

- **`LeaveRequestFactory` missing `policy_id`** — `leave_requests.policy_id` is NOT NULL FK. Added `LeavePolicy::factory()` to factory definition.
- **`EmployeeController::store()` missing `employee_number` auto-generation** — column is NOT NULL. Added auto-generate (`EMP-XXXX`) when not provided. Also auto-defaults `hire_date` to today.

#### Test Expansion — New Service Tests

| Test File | Tests Added |
|---|---|
| `tests/Feature/Finance/InvoiceServiceTest.php` | 5 (create, sent, paid, sequential numbers, company scoped numbers) |
| `tests/Feature/Finance/ExpenseServiceTest.php` | 3 (submit, approve, reject) |
| `tests/Feature/Crm/CrmDealServiceTest.php` | 6 (create, pipeline move, won, lost, seed stages, idempotent) |
| `tests/Feature/Operations/InventoryServiceTest.php` | 5 (add stock, remove, calculate, zero, company scoped) |
| `tests/Feature/Comms/AnnouncementServiceTest.php` | 4 (publish, acknowledge, idempotent ack, multi-user) |
| `tests/Feature/Core/WebhookDeliveryServiceTest.php` | 4 (dispatch filter, delivery, failure count, HMAC signing) |
| `tests/Feature/Api/ApiAuthTest.php` | 5 (issue token, reject invalid, reject unknown, revoke, auth required) |
| `tests/Feature/Api/ProjectApiTest.php` | 6 (list scoped, create, show, 404 cross-tenant, update, delete) |
| `tests/Feature/Api/EmployeeApiTest.php` | 8 (list scoped, create, invalid enum, show, 404 cross-tenant, update, delete) |
| `tests/Feature/Api/TaskApiTest.php` | 8 (list scoped, create, invalid status enum, invalid priority enum, show, 404 cross-tenant, update, delete) |

#### Infrastructure Fix — Docker PHP memory limit

Full test suite (520+ tests) OOM-crashed at 128MB PHP CLI default. Fixed by:
- Added `echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini` to `Dockerfile` (persists in image)
- Applied immediately in running container via same command

**Final result:** 528 tests passed, 1 skipped, 0 failed (1017 assertions).

---

### Session 2026-05-11

**Goal:** Fix all runtime bugs from Phase 3–5 build. Get full test suite to 0 failures. Fix LocalDemoDataSeeder to run cleanly. Build missing Vite assets for Phase 3–5 panels.

**Built / Fixed:**

#### LocalDemoDataSeeder — 5 bugs fixed

- **`KanbanColumn.sort_order`** — seeder used key `position` but migration column is `sort_order`. All 4 column definitions renamed. Also added missing `company_id` to each create call.
  - File: `database/seeders/LocalDemoDataSeeder.php:492–501`
- **`TaxRate.type`** — seeder used `'sales'`, enum is `['vat', 'sales_tax', 'other']`. Fixed to `'vat'`.
  - File: `database/seeders/LocalDemoDataSeeder.php:777–778`
- **`OnboardingTemplateTask`, `OnboardingChecklistItem`, `PayrollEntry`** — missing `company_id` on child record creates. BelongsToCompany trait auto-sets `company_id` in request context but NOT in seeder context (no HTTP request). Fixed by explicit `'company_id' => $cid`.
  - Files: `database/seeders/LocalDemoDataSeeder.php:347,363,404`

**Result:** `php artisan db:seed --class=LocalDemoDataSeeder` runs fully clean.

#### Vite config — 8 missing panel CSS entries

`vite.config.js` only had 4 panel themes (admin, app, hr, projects). Phase 3–5 panels (finance, crm, operations, it, legal, ecommerce, marketing, comms) were missing. Each missing panel caused `ViteException: Unable to locate file in Vite manifest: resources/css/filament/{panel}/theme.css` → HTTP 500 on every page load.

- Added 8 entries to `vite.config.js` input array
- Rebuilt: `npm run build`
- All 12 panels now in manifest

#### Resource bugs — `whereHas('companies')` broken pattern

3 resources used `User::withoutGlobalScopes()->whereHas('companies', fn ($q) => ...)` in Select dropdowns. `User` model has no `companies()` relationship — it uses `company_id` directly. This caused HTTP 500 on any create/edit page that rendered the affected Select.

Fixed all 3:
- `app/Filament/Operations/Resources/FieldJobResource.php:87` → `->where('company_id', ...)`
- `app/Filament/Operations/Resources/PhysicalAssetResource.php:92` → `->where('company_id', ...)`
- `app/Filament/Finance/Resources/ExpenseResource.php:69` → `->where('company_id', ...)`

#### Test enum/constraint fixes — 7 test files, 18 wrong values

Tests used incorrect enum values that violated PostgreSQL check constraints. Root cause: tests were written against assumed enum values, not the actual migration definitions.

| Model | Test used | Migration enum | Fix |
|---|---|---|---|
| `ItAsset.status` | `'active'` | `['available', 'assigned', 'in_repair', 'retired']` | `'available'` |
| `LegalPolicy.status` | `'published'` | `['draft', 'active', 'archived']` | `'active'` |
| `RiskRegister.status` | `'open'` | `['identified', 'assessed', 'mitigated', 'accepted', 'closed']` | `'identified'` |
| `Dsar.status` | `'open'` | `['received', 'in_progress', 'completed', 'rejected']` | `'received'` |
| `ItChangeRequest.status` | `'pending'` | `['draft', 'submitted', 'approved', 'in_progress', 'completed', 'rejected']` | `'submitted'` |
| `ItChangeRequest.type` | `'major'` | `['standard', 'normal', 'emergency']` | `'standard'` |
| `InventoryLocation.type` | `'shelf'` | `['warehouse', 'store', 'virtual']` | `'warehouse'` |
| `PhysicalAsset.status` | `'operational'` | `['available', 'in_use', 'under_maintenance', 'disposed']` | `'available'` |
| `CrmContact.status` | `'active'` | `['lead', 'prospect', 'customer', 'lost']` | `'lead'` |
| `MarketingEvent.status` | `'upcoming'` | `['draft', 'published', 'cancelled']` | `'published'` |
| `Expense.status` | `'pending'` | `['draft', 'submitted', 'approved', 'rejected', 'reimbursed']` | `'submitted'` |
| `TaxRate.type` | `'sales'` | `['vat', 'sales_tax', 'other']` | `'vat'` |

#### Test NOT NULL fixes

Several tests created records missing required fields:

- `CrmTicket` — missing `description` (NOT NULL). Added `'description' => 'User cannot log in.'`
- `ItTicket` — missing `description` (NOT NULL). Added.
- `LegalContract` (edit test) — missing `counterparty_name` (NOT NULL). Added.
- `ItChangeRequest` — missing `description` (NOT NULL). Added to all 3 create calls.
- `FixedAsset` — missing `useful_life_years` (NOT NULL integer) and `current_book_value` (NOT NULL decimal). Added both.
- `LeaveRequest` — missing `policy_id` (NOT NULL FK). Factory doesn't provide it. Fixed test to create `LeavePolicy::factory()->create()` first and pass its `id`.

#### Test assertion fix

- `ProjectsResourceCrudTest` TimeEntry cross-tenant test used `assertDontSee('99')` — the number `99` appears naturally in HTML (pagination counts, etc.). Changed to use unique description string `'PrivateCorpTimeEntry unique string XYZ'` with `assertDontSee`.

#### Seeder/permission count update

Phase 3–5 added 98 new permissions. Tests hardcoded old count of 69.
- `tests/Feature/Seeders/PermissionSeederTest.php` — updated `69` → `167` in 5 places
- `tests/Feature/Seeders/LocalSeederTest.php` — updated `69` → `167` in 1 place

**Final result:** 489 tests, 1066 assertions, 0 failures.

---

## Bugs Found and Fixed

### Bug 1: KanbanColumn `sort_order` vs `position` mismatch
- **File:** `database/seeders/LocalDemoDataSeeder.php:492–501`
- **Problem:** Migration uses `sort_order`, seeder uses `position`. Also missing `company_id`.
- **Fix:** Renamed key + added `company_id`.
- **Severity:** Medium (seeder crash, no data for demo)

### Bug 2: TaxRate type enum mismatch in seeder
- **File:** `database/seeders/LocalDemoDataSeeder.php:777`
- **Problem:** Seeder uses `'sales'`, migration enum is `['vat', 'sales_tax', 'other']`.
- **Fix:** Changed to `'vat'`.
- **Severity:** Medium (seeder crash)

### Bug 3: `whereHas('companies')` — User model has no such relation
- **Files:** FieldJobResource, PhysicalAssetResource, ExpenseResource
- **Problem:** `User` model has no `companies()` relationship. Only `company_id`. This caused HTTP 500 on create/edit pages.
- **Fix:** Replaced `whereHas('companies', fn...)` with `where('company_id', ...)`.
- **Severity:** High (feature completely broken in production)

### Bug 4: Vite manifest missing 8 panel themes
- **File:** `vite.config.js`
- **Problem:** Only Phase 0–2 panels in Vite input. All Phase 3–5 panel create/list pages returned HTTP 500.
- **Fix:** Added 8 CSS entries to `vite.config.js`, ran `npm run build`.
- **Severity:** High (all Phase 3–5 pages broken in production)

---

## Gaps Discovered

None new. All bugs were implementation errors, not spec gaps.

---

## Architectural Notes

**BelongsToCompany trait in seeder context:** The trait's `boot()` method reads `company_id` from `app(CompanyContext::class)->currentId()`. In an HTTP request, this is set by middleware. In a seeder (no HTTP), it returns null. Child record creates in seeders must ALWAYS pass `'company_id' => $cid` explicitly.

**User model — no `companies()` relation:** Users belong to exactly one company via `company_id`. There is no many-to-many pivot. Any code using `whereHas('companies', ...)` on User will fail with "Relationship does not exist". Pattern: `User::withoutGlobalScopes()->where('company_id', $cid)->pluck(...)`.

**Test enum values must match migrations exactly:** PostgreSQL check constraints are strict. Tests should not assume/guess enum values — always verify against the migration before writing a create call in a test.
