---
tags: [brain, domain, core]
last_updated: 2026-05-07
---

# Domain — Core Platform

**Spec:** `01 - Core Platform/` — Authentication & Identity, Roles & Permissions, Notifications & Alerts, API & Integrations, Multi-Tenancy & Workspace, File Storage  
**Panels:** `admin` (`/admin`, guard `web`) + `workspace` (`/workspace`, guard `tenant`)  
**Root models:** `app/Models/` (not in a subdomain subfolder)

---

## Core Models

### Company
**Table:** `companies`  
**Guard:** none — admin-side model (used by `User` and `Tenant` but has no auth of its own)  
**Traits:** `HasFactory`, `HasUlids`, `SoftDeletes`, `LogsActivity`, `InteractsWithAddresses`  
**Implements:** `HasAddresses`

**Key fields:**
- `name`, `slug` (URL-safe, unique)
- `email`, `phone`, `website`
- `timezone` (IANA, e.g. `Europe/Amsterdam`), `locale` (language code), `currency` (ISO 4217)
- `settings` (JSON array — arbitrary feature flags and config per company)
- `is_enabled` (bool — disabled companies cannot log in)
- `logo_file_id` (nullable → `File`)

**Casts:** `settings` → array, `is_enabled` → boolean

**Relations:**
- `tenants()` → HasMany `Tenant`
- `modules()` → BelongsToMany `Module` via `company_module` pivot (using `CompanyModule` pivot model)
- `logo()` → BelongsTo `File`
- `addresses()` → MorphMany `Address` (via `InteractsWithAddresses`)

**Notable methods:**
- `hasModuleForPanel(string $panelId): bool` — checks `company_module.is_enabled` + `Module.panel_id`; cached per company+panel for 5 min in Redis
- `setting(string $key, mixed $default = null): mixed` — reads from JSON `settings` column
- `logoUrl(): ?string` — returns signed temporary URL via `FileStorageService`; never exposes raw S3 path

**Activity log:** `logOnly` on non-sensitive fillable fields. `settings` and `logo_file_id` excluded.

---

### Tenant
**Table:** `tenants`  
**Guard:** `tenant` — this model is the auth user for all workspace + domain panels (hr, projects, finance, crm)  
**Traits:** `HasFactory`, `HasRoles` (Spatie), `HasUlids`, `SoftDeletes`, `LogsActivity`, `Notifiable`, `InteractsWithAddresses`  
**Implements:** `FilamentUser`, `HasAddresses`, `HasName`

**Key fields:**
- `company_id` — which workspace this tenant belongs to
- `first_name`, `middle_name` (nullable), `last_name`
- `email`, `phone` (nullable)
- `password` → `hashed` cast
- `is_enabled` (bool — disabling prevents login without deleting)

**Casts:** `password` → hashed, `is_enabled` → boolean

**Relations:**
- `company()` → BelongsTo `Company`
- `notificationPreferences()` → HasMany `NotificationPreference`
- Roles via `HasRoles` (Spatie `spatie/laravel-permission`)
- `addresses()` → MorphMany `Address`

**Notable methods:**
- `canAccessPanel(Panel $panel): bool` — checks `is_enabled` + `company.is_enabled`; workspace panel always allowed; other panels check `company.hasModuleForPanel($panel->getId())`; result cached 5 min in Redis as `company:{id}:panel:{id}:access`
- `fullName(): string` — joins `first_name`/`middle_name`/`last_name` with null filtering
- `getFullNameAttribute(): string` — Eloquent accessor wrapping `fullName()` (added in Phase 3 gap-fill)
- `getFilamentName(): string` — used by Filament header display
- `setting(string $key, mixed $default = null)` — proxies to `company->setting()`

**No `BelongsToCompany` trait** — `Tenant` IS the auth model; global scope only fires when tenant is authenticated, never applies to Tenant itself. Always scope Tenant dropdowns manually to `company_id`.

---

### User
**Table:** `users`  
**Guard:** `web` — FlowFlex super-admin only. No access to workspace panels.  
**Traits:** `HasUlids`, `SoftDeletes`, `LogsActivity`

**Key fields:** `name`, `email`, `password` (hashed cast), `is_enabled` (bool)

**Access:** Admin panel only (`/admin`). Cannot access workspace, hr, projects, finance, or crm panels.

---

### ApiKey
**Table:** `api_keys`  
**Traits:** `HasUlids`, `SoftDeletes`, `LogsActivity`, `BelongsToCompany`

**Key fields:**
- `company_id`
- `name` (human-readable label, e.g. "Zapier Integration")
- `key_hash` → `hashed` cast (the actual key is hashed; only shown once on creation)
- `key_prefix` (string — first 8 chars, shown in the UI for identification)
- `last_used_at` (datetime, nullable)
- `expires_at` (datetime, nullable — null = never expires)

**Auth flow:**  
1. Client sends `Authorization: Bearer {raw_key}` header  
2. `AuthenticateApiKey` middleware finds key by prefix (`key_prefix`), verifies hash  
3. Eager-loads `company` via `->with('company')` — prevents N+1  
4. Sets `request()->attributes->put('api_company', $key->company)` and `request()->attributes->put('api_key', $key)`  
5. Controllers read `$request->attributes->get('api_company')` — never `auth()->user()`

**Permissions:** API is read-only (GET only). No write endpoints in v1.

---

### Module
**Table:** `modules`  
**Traits:** `HasUlids`, `LogsActivity`  
**No `BelongsToCompany`** — modules are platform-wide definitions, not per-company.

**Key fields:**
- `key` (string — unique slug: `hr`, `projects`, `finance`, `crm`, etc.)
- `name` (display name), `description`
- `domain` (string — groups modules: `hr`, `projects`, `finance`, `crm`, `operations`, etc.)
- `panel_id` (string — which Filament panel this module activates)
- `icon`, `color`, `sort_order` (int)
- `is_core` (bool — core modules cannot be deactivated), `is_available` (bool — visible on marketing site)

**Relations:**
- `subModules()` → HasMany `SubModule` (ordered by `sort_order`)
- `companies()` → BelongsToMany `Company` via `company_module` pivot (using `CompanyModule`)

---

### SubModule
**Table:** `sub_modules`  
**Traits:** `HasUlids`, `LogsActivity`

**Key fields:** `module_id`, `name`, `description`, `sort_order` (int)

**Relations:**
- `module()` → BelongsTo `Module`

---

### CompanyModule (Pivot)
**Table:** `company_module`  
**File:** `app/Models/Pivots/CompanyModule.php`  
**Traits:** `HasUlids`, `SoftDeletes`, `LogsActivity`  
**Extends:** `Illuminate\Database\Eloquent\Relations\Pivot`  
**Purpose:** The pivot model for company ↔ module activation. Must be a model (not just a table) to get `HasUlids` auto-generation.

**Pivot fields:**
- `company_id`, `module_id`
- `is_enabled` (bool — whether this company has this module active)
- `enabled_at` (datetime, nullable), `disabled_at` (datetime, nullable)

---

### File
**Table:** `files`  
**Traits:** `HasUlids`, `SoftDeletes`, `LogsActivity`, `BelongsToCompany`

**Key fields:**
- `company_id`
- `disk` (string: `s3` or `local`)
- `path` (string — storage path on the disk; **never expose this directly**)
- `original_name` (string — original filename from upload)
- `mime_type`, `size_bytes` (int)
- `uploaded_by_tenant_id` (nullable)

**Access rule:** Always use `FileStorageService::temporaryUrl($file)` or `$file->url()`. Never pass `$file->path` to the frontend. Raw S3 paths are not signed and expose internal storage structure.

**Relations:**
- `uploadedBy()` → BelongsTo `Tenant`

**Services:**
- `app/Services/FileStorageService.php` — abstracts S3/local disk. Method `temporaryUrl(File $file, ?Carbon $expiry): string` returns signed URL (default 15 min expiry).

---

### NotificationPreference
**Table:** `notification_preferences`  
**Traits:** `HasUlids`, `BelongsToCompany`, `LogsActivity`  
**Purpose:** Per-tenant per-channel per-notification-type toggle. Tenants can disable specific notification types on specific channels.

**Fillable fields:**
- `company_id`, `tenant_id`
- `channel` (string: email/in_app/sms)
- `notification_type` (string — matches notification class name, e.g. `LeaveApproved`)
- `is_enabled` (bool)

**Relations:**
- `tenant()` → BelongsTo `Tenant`

---

### Address
**Table:** `addresses`  
**Traits:** `HasUlids`, `SoftDeletes`, `LogsActivity`  
**Purpose:** Polymorphic address record. Used by Company and Tenant via `InteractsWithAddresses` concern.

**Fillable fields:**
- `addressable_type`, `addressable_id` (polymorphic morph)
- `line_1`, `line_2` (nullable), `city`, `state` (nullable), `postcode`, `country` (ISO 2-char)
- `type` (string: billing/shipping/registered, nullable)

**Relations:**
- `addressable()` → MorphTo (resolves to Company or Tenant)

---

## Marketing Models (Admin-Managed CMS)

**Spec:** `14 - Marketing Site/`  
All in `app/Models/Marketing/`. All have `LogsActivity` + `getActivitylogOptions()`. No `BelongsToCompany` — marketing content is platform-wide.

| Model | Table | Purpose | Key Fields |
|---|---|---|---|
| `BlogPost` | `blog_posts` | Blog articles with SEO fields | `title`, `slug`, `body`, `category_id`, `is_published`, `published_at`, `seo_title`, `seo_description`, `author_name`, `read_time_minutes` |
| `BlogCategory` | `blog_categories` | Blog taxonomy | `name`, `slug`, `description` |
| `ChangelogEntry` | `changelog_entries` | Product release notes | `version`, `title`, `body`, `type` (feature/fix/improvement/security), `published_at`, `is_published` |
| `ContactSubmission` | `contact_submissions` | Contact form leads (read-only in admin) | `name`, `email`, `company`, `subject`, `message`, `is_handled` |
| `DemoRequest` | `demo_requests` | Demo booking pipeline | `first_name`, `last_name`, `email`, `company`, `team_size`, `pain_points`, `is_contacted` |
| `FaqEntry` | `faq_entries` | FAQ page content | `question`, `answer`, `category`, `sort_order`, `is_published` |
| `HelpArticle` | `help_articles` | Help centre articles | `help_category_id`, `title`, `slug`, `body` (NOT NULL), `seo_title`, `seo_description`, `is_published`, `display_order`, `helpful_count`, `not_helpful_count`, `last_reviewed_at` |
| `HelpCategory` | `help_categories` | Help centre navigation | `name`, `slug`, `description`, `icon`, `parent_id` (nested), `display_order`, `is_published` |
| `NewsletterSubscriber` | `newsletter_subscribers` | Email list | `email`, `first_name`, `subscribed_at`, `is_active`, `source` |
| `OpenRole` | `open_roles` | Careers page jobs | `title`, `department`, `location`, `type` (full-time/part-time/contract), `description`, `is_published`, `published_at` |
| `TeamMember` | `team_members` | About page team bios | `name`, `job_title`, `bio`, `photo_url`, `linkedin_url`, `display_order`, `is_published` |
| `Testimonial` | `testimonials` | Social proof | `name`, `company`, `role`, `quote`, `rating` (1-5), `photo_url`, `is_featured`, `is_published` |

**HelpArticle notes:**
- `body` column is NOT NULL — always include in test fixtures
- `display_order` column for ordering within category
- Relation `category()` uses explicit FK `help_category_id`

**HelpCategory notes:**
- Self-referential: `parent_id` → self (`parent()` BelongsTo, no inverse)
- Column is `display_order` (not `sort_order`)

---

## Panels

### Admin Panel (`/admin`)
**Guard:** `web` (User model)  
**Colour:** `#2199C8`  
**Spec:** `Filament Panels/Panel Map.md`

**Resources:**
- Core: CompanyResource, TenantResource, UserResource, ModuleResource, RoleResource, PermissionResource
- Marketing CMS: BlogPostResource, BlogCategoryResource, ChangelogEntryResource, ContactSubmissionResource, DemoRequestResource, FaqEntryResource, HelpArticleResource, HelpCategoryResource, NewsletterSubscriberResource, OpenRoleResource, TeamMemberResource, TestimonialResource

**Widgets:**
- `AdminStatsOverviewWidget` — company, tenant, user counts
- `RecentCompaniesWidget` — latest sign-ups
- `MarketingStatsWidget` — blog posts, demo requests, subscribers

---

### Workspace Panel (`/workspace`)
**Guard:** `tenant`  
**Colour:** none (inherits Filament default)  
**Always accessible** — regardless of which modules are active. Every enabled tenant has workspace access.

**Pages (all extend `Filament\Pages\Page`):**
- `ManageCompany` — edit company name, logo, timezone, locale, currency
- `ManageTeam` — invite/disable team members, assign roles
- `ManageApiKeys` — create/revoke API keys (key shown once, stored as hash)
- `ManageNotificationPreferences` — per-tenant per-channel toggles

**Navigation icon rule:** Pages in workspace settings have NO `$navigationIcon` — the navigation group has an icon, and Filament 5 throws if both group and item have icons.

---

## Services

### FileStorageService
`app/Services/FileStorageService.php`  
**Purpose:** Abstracts S3 / local disk. All file URL generation goes through here.

**Key methods:**
- `temporaryUrl(File $file, Carbon $expiry = null): string` — returns signed URL, default 15 min expiry
- Used by `Company::logoUrl()` and all resource views that display files

**Rule:** Never expose `$file->path` directly. Always call this service or `$file->url()` (which calls this service internally).

---

## Auth Architecture

| Who | Model | Guard | Logs into |
|---|---|---|---|
| FlowFlex super-admin | `User` | `web` | `/admin` |
| Company workspace user (employee, manager, etc.) | `Tenant` | `tenant` | `/workspace`, `/hr`, `/projects`, `/finance`, `/crm` |
| API consumer | via `ApiKey` | none (middleware) | `/api/v1/*` |

**No self-registration.** Admin creates first `User`. Company owner (Tenant with admin role) adds other Tenants via workspace settings. OAuth + SAML deferred to Phase 8.

---

## RBAC (Spatie Permissions)

Roles and permissions are defined in `database/seeders/RolesAndPermissionsSeeder.php`.

**Permission naming:** `{module}.{resource}.{action}`  
Examples: `hr.employees.view`, `finance.invoices.send`, `crm.tickets.resolve`, `projects.task-labels.edit`

**Built-in roles:** `super-admin`, `workspace-admin`, `hr-manager`, `finance-manager`, `sales-rep`, `employee`

**Every Filament Resource implements:**
```php
public static function canViewAny(): bool   // uses auth()->user()?->can('{module}.{resource}.view')
public static function canCreate(): bool
public static function canEdit($record): bool
public static function canDelete($record): bool
```

**Policy pattern:** All policies use `Tenant $tenant` as the user argument (not `User`). Every policy checks `$tenant->company_id === $record->company_id` before checking permission — prevents cross-company access even if permission is granted.

---

## API (`/api/v1`)

**Middleware:** `AuthenticateApiKey` → `throttle:60,1`  
**Auth:** `Authorization: Bearer {raw_api_key}` header  
**All routes:** Read-only GET only. No write endpoints in v1.

**Company context in controllers:**
```php
$company = $request->attributes->get('api_company'); // NEVER auth()->user()
```

Full endpoint list in [[Current State]].

---

## Events (Phase 1)

| Event | Listener | Type |
|---|---|---|
| `UserLoggedIn` | `LogUserLogin` | stub |
| `ModuleActivated` | `NotifyModuleActivated` | stub |
| `TenantCreated` | `SendWelcomeEmail` | stub |
