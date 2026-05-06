# CLAUDE.md — FlowFlex Platform

> **Single source of truth is the Obsidian vault** at `obsidian/` in this repo.
> Before writing code, check the relevant vault note. Before making architecture decisions, check the vault.
> This file contains only the critical rules needed for every code change — detail lives in Obsidian.

---

## What is FlowFlex?

Modular, multi-tenant SaaS platform. Customers activate only the modules they need and pay per module. One login, one data layer, one bill. See `obsidian/00 - Project Overview/FlowFlex Overview.md`.

**Stack:** Laravel 12 · Filament 5 · PostgreSQL · Redis · AWS S3/R2 · Stripe · Pusher

---

## Obsidian Vault — Where to Look

| Topic | Vault note |
|---|---|
| Full tech stack | `00 - Project Overview/Tech Stack.md` |
| Architecture & module structure | `00 - Project Overview/Architecture.md` |
| Multi-tenancy implementation | `00 - Project Overview/Multi-Tenancy.md` |
| Naming conventions | `00 - Project Overview/Naming Conventions.md` |
| Security rules | `00 - Project Overview/Security Rules.md` |
| Performance rules | `00 - Project Overview/Performance Rules.md` |
| Module dev checklist | `00 - Project Overview/Module Development Checklist.md` |
| Cross-module events | `00 - Project Overview/Cross-Module Event Map.md` |
| Build phase status | `00 - Project Overview/Build Order (Phases).md` |
| Environment setup | `00 - Project Overview/Environment Setup.md` |
| Filament panels & access rules | `Filament Panels/Panel Map.md` |
| Every module spec | `01–13` domain folders |
| Design system & brand | `Design System/` folder |

---

## Non-Negotiable Rules (memorise these — apply to every line of code)

### Models
Every model gets: `HasUlids`, `SoftDeletes`, `LogsActivity`, `BelongsToCompany`.
ULID primary keys throughout. No auto-increment, no UUID.

### Multi-Tenancy
- `Company` = workspace entity (one per business)
- `Tenant` = workspace user (employee / member) — uses `tenant` guard
- `User` = FlowFlex super-admin — uses `web` guard
- Column is `company_id` (not `tenant_id`) — `BelongsToCompany` trait adds global scope automatically
- **Never bypass the global scope.** If querying across companies deliberately, use `withoutGlobalScopes()` and re-apply `company_id` manually.

### Auth Guards
- Workspace panels → `authGuard('tenant')`
- Admin panel → default `web` guard
- API → `AuthenticateApiKey` middleware (sets `api_company` + `api_key` on request)

### Permissions
Naming: `{module}.{resource}.{action}` e.g. `hr.employees.view`, `workspace.settings.edit`.
Every Filament Resource implements `canViewAny()`, `canCreate()`, `canEdit()`, `canDelete()`.

### Filament 5
Use `Filament\Schemas\Schema` — **not** `Filament\Forms\Form` (Filament 3 API).

### File Access
Never expose raw S3 paths. Always `$file->url()` or `FileStorageService::temporaryUrl()`. See `obsidian/01 - Core Platform/File Storage.md`.

### Security
- All user input → Laravel Form Requests with explicit validation
- Sensitive fields (API keys, bank details, salary) → `encrypted` cast
- All writes → Spatie Activity Log (`LogsActivity` trait on every model)
- No public API endpoints unless intentional

### Performance
- No N+1 — always eager-load with `with()`
- Slow jobs (PDF, email, reports, sync) → queue, never block HTTP
- Cache module registry + permissions in Redis per company; bust on change
- Paginate all table views (default 25 rows)

### Account Creation
No self-registration. First account created by FlowFlex super-admin. Company owner adds members via workspace settings. OAuth and SAML deferred to post-launch.

---

## MVP Build Status

See `obsidian/00 - Project Overview/Build Order (Phases).md` for current status.

Phase 1 (Foundation) in progress:
- ✅ Auth, RBAC, Multi-tenancy, Notifications, API, Workspace Settings, File Storage
- ⏳ Module Billing Engine — deferred to Phase 6

---

*Maintained by: Max (Founder)*
*Last updated: May 2026*
*For full detail on any topic → check the Obsidian vault first*
