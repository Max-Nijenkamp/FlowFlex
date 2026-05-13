# FlowFlex — Claude Instructions

## Project

FlowFlex is an all-in-one SaaS platform. This repo contains:
- `vault/` — Obsidian knowledge vault (specs, architecture, domain docs, build tracking)
- `app/` — Laravel 13 + Filament 5 PHP application (when built)

## Vault Structure

```
vault/
├── _meta/                   # Entry points and templates (gray #6B7280)
│   ├── HOME.md              # Single entry point — start here
│   ├── graph-config.md      # Obsidian graph color setup
│   └── templates/           # tpl_module, tpl_domain-index, tpl_build-log, tpl_gap, tpl_adr
├── product/                 # Brand, positioning, UX, pricing (sky blue #38BDF8)
├── architecture/            # Tech stack, patterns, data model (purple #A78BFA)
│   └── patterns/            # Detailed code patterns (interface-service, dto, tenancy, testing)
├── frontend/                # Public Vue+Inertia pages (amber #FBBF24)
├── domains/                 # 32 domain specs — source of truth for what to build (green #4ADE80)
│   ├── INDEX.md             # All 32 domains table
│   └── {domain}/            # One folder per domain, e.g. hr/, projects/, finance/
│       ├── INDEX.md         # Domain overview and module list
│       └── {module}.md      # One file per module spec
└── build/                   # Build tracking — progress, logs, gaps, decisions (orange #F97316)
    ├── STATUS.md            # Per-domain progress dashboard (0/243 at start)
    ├── ACTIVATION.md        # How to run a build session
    ├── logs/                # Per-session build logs (one file per session)
    ├── gaps/                # Bugs and missing specs discovered during build
    │   └── INDEX.md         # Gap index table
    └── decisions/           # Architectural decision records (ADRs)
        └── INDEX.md         # Decision log table
```

## Graph Colors

Each section has its own Obsidian graph color driven by the `color:` frontmatter field:

| Section | Color | Hex |
|---|---|---|
| `product/` | Sky blue | `#38BDF8` |
| `architecture/` | Purple | `#A78BFA` |
| `domains/` | Green | `#4ADE80` |
| `build/` | Orange | `#F97316` |
| `frontend/` | Amber | `#FBBF24` |
| `_meta/` | Gray | `#6B7280` |

**Every file in `build/` MUST include `color: "#F97316"` in frontmatter.**  
**Every file in `domains/` MUST include `color: "#4ADE80"` in frontmatter.**

## FlowFlex Slash Commands

These commands keep specs and build tracking in sync.

### `/flowflex:sync`
**Use after every build session or when modifying a domain spec.**

Steps:
1. Identify: module name, domain, panel, status (in-progress/complete), bugs found, decisions made
2. **Update domain spec frontmatter** — set `status:` to `in-progress` or `complete`
3. **Create or update build log** at `vault/build/logs/{domain}-{YYYY-MM-DD}.md`:
   - Use template from `vault/_meta/templates/tpl_build-log.md`
   - Frontmatter MUST include `color: "#F97316"`, `type: build-log`
   - Session entry: what was built (file paths), decisions, problems, gaps discovered
4. **Update `vault/build/STATUS.md`** — update Built count, emoji, recalculate %, add row to Recent Sessions
5. **Create gap files** for any bugs/spec issues — `vault/build/gaps/gap-{slug}.md` with `color: "#F97316"` + update `vault/build/gaps/INDEX.md`
6. **Create ADR files** for architectural decisions — `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md` with `color: "#F97316"` + update `vault/build/decisions/INDEX.md`

### `/flowflex:done [module=name]`
**Use when a module is fully built and tested.**

Steps:
1. Run all sync steps above with `status: complete`
2. Increment domain Built count in `vault/build/STATUS.md`
3. Update domain spec `status: complete`
4. Link any validation notes from the build log

### `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]`
**Use when you find a bug or spec gap.**

Steps:
1. Create `vault/build/gaps/gap-{slug}.md` with frontmatter:
   ```yaml
   ---
   type: gap
   severity: high | medium | low
   category: spec | architecture | feature | bug | data-model
   status: open
   color: "#F97316"
   discovered: YYYY-MM-DD
   discovered-in: {module-name}
   last-updated: YYYY-MM-DD
   ---
   ```
2. Document: context, the problem, impact, proposed solution, links
3. Add row to `vault/build/gaps/INDEX.md` Open Gaps table
4. If in a build session, link the gap from the active build log under `## Gaps Discovered`

### `/flowflex:decision ["title"] [status=decided|proposed]`
**Use when an architectural decision is made.**

Steps:
1. Create `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md` with frontmatter:
   ```yaml
   ---
   type: adr
   date: YYYY-MM-DD
   status: decided | proposed
   color: "#F97316"
   ---
   ```
2. Document: context, options considered, decision, consequences, related domain specs
3. Update related domain specs to reflect the decision
4. Add row to `vault/build/decisions/INDEX.md` Decision Log table

### `/flowflex:status [domain=name] [full]`
**Use to check current build state.**

Steps:
1. Read `vault/build/STATUS.md` — show domain progress table
2. If `domain=name`: show that domain's module list with status from `vault/domains/{domain}/INDEX.md`
3. If `full`: also show open gaps (`vault/build/gaps/INDEX.md`) and recent decisions (`vault/build/decisions/INDEX.md`)

## Key Conventions

### Domain Specs (`vault/domains/`)
- `status:` values: `planned` | `in-progress` | `complete`
- Module spec frontmatter (exact format — no phase, no migration_range, no last_updated):
  ```yaml
  ---
  type: module
  domain: HR & People
  panel: hr
  module-key: hr.profiles
  status: planned
  color: "#4ADE80"
  ---
  ```
- When starting a build, update `status: in-progress` in the spec
- When complete, update `status: complete`
- No other frontmatter fields needed

### Build Logs (`vault/build/logs/`)
- One file per build session, named `{domain}-{YYYY-MM-DD}.md`
- Use template: `vault/_meta/templates/tpl_build-log.md`
- Required frontmatter:
  ```yaml
  ---
  type: build-log
  domain: HR & People
  panel: hr
  session-date: YYYY-MM-DD
  status: in-progress
  color: "#F97316"
  ---
  ```

### Gaps (`vault/build/gaps/`)
- Named `gap-{slug}.md`, e.g. `gap-hr-leave-overlap.md`
- Index at `vault/build/gaps/INDEX.md`

### Decisions (`vault/build/decisions/`)
- Named `decision-{YYYY-MM-DD}-{slug}.md`
- Index at `vault/build/decisions/INDEX.md`

## Auto-Trigger Rules

After any domain spec edit, run `/flowflex:sync` at the end of the task.

## Tech Stack

- **PHP 8.4** + **Laravel 13**
- **Filament 5** (32 panels: one per domain at `/app/{domain}`, plus `/admin` for FlowFlex staff)
- **PostgreSQL 17** + **Redis 8** + **Meilisearch 1.x**
- **Vue 3.5** + **TypeScript 5** + **Inertia.js v2** (public frontend)
- **Livewire v4** + **Alpine.js 3** (Filament components)
- **Vite 6** + **Tailwind CSS v4**
- **Laravel Horizon 5.x** (queues) + **Laravel Reverb 1.x** (WebSockets)
- **Laravel Pulse 1.x** (metrics) + **Laravel Telescope** (dev only)
- **Laravel Sanctum 4.x** (API auth)
- **spatie/laravel-data 4.x** (DTOs — `app/Data/{Domain}/`)
- **spatie/laravel-permission 6.x** (RBAC with teams = company_id)
- **spatie/laravel-activitylog 5.x** (audit trail)
- **spatie/laravel-media-library 11.x** (file storage)
- **spatie/laravel-typescript-transformer 2.x** (DTO → TypeScript)
- **stripe/stripe-php 14.x** (billing)
- **bezhansalleh/filament-shield** (Filament permission UI)
- **chart.js 4.x** + **@tiptap/vue-3 2.x** + **zod 3.x**
- ULID primary keys everywhere
- Global CompanyScope for multi-tenancy (`company_id` on all tables)

## Key App Directory Structure

```
app/
├── Contracts/{Domain}/     # Service interfaces
├── Services/{Domain}/      # Concrete implementations
├── Providers/{Domain}/     # ServiceProviders binding Interface → Service
├── Http/Controllers/       # Thin Inertia controllers (<10 lines each)
├── Data/{Domain}/          # spatie/laravel-data DTOs (input + output)
├── Models/                 # Eloquent models (HasUlids, BelongsToCompany, SoftDeletes)
├── Events/                 # Domain events (always carry company_id)
├── Filament/
│   ├── Admin/              # /admin panel resources (FlowFlex staff)
│   └── App/                # /app panel resources (tenant users, 32 panels)
└── Support/
    ├── Traits/BelongsToCompany.php
    ├── Traits/HasUlid.php
    ├── Scopes/CompanyScope.php
    └── Services/CompanyContext.php
```

## Architecture Patterns (read before building)

Before building any module, read:
1. `vault/architecture/filament-patterns.md` — 10 critical Filament 5 patterns (canAccess, getSlug, theme, etc.)
2. `vault/architecture/patterns/interface-service.md` — Interface → ServiceProvider → thin controller
3. `vault/architecture/patterns/dto-pattern.md` — spatie/laravel-data input/output DTOs
4. `vault/architecture/patterns/belongs-to-company.md` — HasUlids, BelongsToCompany, SoftDeletes traits
5. `vault/architecture/patterns/testing-pattern.md` — Pest, SQLite in-memory, CompanyContext setup
6. `vault/architecture/multi-tenancy.md` — CompanyScope, queue context, Spatie Permission teams
