---
type: domain-index
domain: Foundation
panel: (scaffold)
color: "#4ADE80"
---

# Foundation

Application scaffold — invisible to company users, required by everything else. No Filament panel for this domain.

**Build this first.** Nothing in Phase 1+ works without it.

---

## Modules

| Module | Key | Status | Description |
|---|---|---|---|
| [[domains/foundation/laravel-scaffold\|Laravel Scaffold]] | — | planned | Laravel 13 project, packages, config, ULID keys, queue and broadcast config |
| [[domains/foundation/filament-panels\|Filament Panels]] | — | planned | `/admin` panel (FlowFlex staff) + `/app` panel (tenant users) base setup |
| [[domains/foundation/multi-tenancy-layer\|Multi-Tenancy Layer]] | — | planned | `BelongsToCompany` trait, `CompanyScope`, `CompanyContext` singleton |
| [[domains/foundation/docker-environment\|Docker Environment]] | — | planned | Docker Compose: nginx, PHP-FPM, PostgreSQL 17, Redis 8, Mailpit, Horizon, Reverb |
| [[domains/foundation/queue-workers\|Queue Workers & Scheduler]] | — | planned | Horizon setup, named queues, WithCompanyContext middleware, scheduled commands |
| [[domains/foundation/test-suite\|Test Suite]] | — | planned | Pest PHP, SQLite in-memory isolation, factory states, arch tests, Livewire helpers |
| [[domains/foundation/email-setup\|Email Setup]] | — | planned | FlowFlexMailable base class, Resend config, bounce handling, mail queue config |
| [[domains/foundation/permissions-seed\|Permissions Seeder]] | — | planned | PermissionSeeder + ModuleCatalogSeeder + LocalDevSeeder — idempotent install scripts |

---

## Key Constraints

- No public company registration — companies created by FlowFlex staff in `/admin`
- All tenant models carry `company_id` + `BelongsToCompany` trait
- ULID PKs on every table
- `spatie/laravel-permission` with `teams = true` — roles scoped to `company_id`
- Two completely separate Filament guards: `admin` (Admin model) and `web` (User model)

## Key Patterns

- [[architecture/multi-tenancy]] — full multi-tenancy implementation
- [[architecture/filament-patterns]] — panel provider and resource conventions
- [[architecture/patterns/belongs-to-company]] — model trait requirements
- [[architecture/patterns/testing-pattern]] — test suite setup
