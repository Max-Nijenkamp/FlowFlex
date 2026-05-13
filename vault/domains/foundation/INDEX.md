---
type: domain-index
domain: Foundation
panel: (scaffold — no panel)
panel-path: /
panel-color: Slate
color: "#4ADE80"
---

# Foundation

Foundation is the application scaffold, not a business domain. No company user sees "Foundation" anywhere in the product — it is invisible infrastructure. It covers the Laravel 13 + Filament 5 project setup, the multi-tenancy layer that isolates every tenant's data, the Docker Compose local development environment, and the Pest PHP test suite conventions that gate every build phase.

All other domains and modules are built on top of Foundation. Nothing in Phase 1+ works without it.

## Modules

| Module | File | Description |
|---|---|---|
| Laravel Scaffold | [[laravel-scaffold]] | Laravel 13 project: packages, config, ULID keys, queue and broadcast config |
| Filament Panels | [[filament-panels]] | Two Filament panels — /admin (FlowFlex staff) and /app (tenant users) |
| Multi-Tenancy Layer | [[multi-tenancy-layer]] | BelongsToCompany trait, CompanyScope global scope, CompanyContext singleton |
| Docker Environment | [[docker-environment]] | Docker Compose: nginx, PHP-FPM, PostgreSQL 17, Redis 8, Mailpit, Horizon, Reverb |
| Test Suite | [[test-suite]] | Pest PHP framework, SQLite isolation, login patterns, factory conventions |

## Key Constraints

- No public registration pages — companies are created by FlowFlex staff in /admin
- All tenant models carry `company_id` and use `CompanyScope` global scope
- ULID primary keys on every table
- `spatie/laravel-permission` with `teams = true` — roles are scoped to `company_id`
- Two completely separate Filament guards: `admin` (Admin model) and `web` (User model)
