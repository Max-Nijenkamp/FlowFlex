# FlowFlex — Claude Instructions

## Project

FlowFlex is an all-in-one SaaS platform. This repo contains:
- `flowflex-vault/` — Obsidian knowledge vault (specs, architecture, domain docs)
- `app/` — Laravel 13 + Filament 5 PHP application (when built)

## Vault Structure

```
flowflex-vault/
├── left-brain/          # Specs, architecture, domain definitions (source of truth)
│   ├── architecture/    # System design docs
│   ├── domains/         # 32 domains (00 Foundation + 31 business domains), each a Filament panel
│   ├── entities/        # Core data model entities
│   ├── concepts/        # Cross-cutting patterns
│   ├── design-system/   # Brand, colours, components
│   ├── frontend/        # Public Vue+Inertia pages
│   └── roadmap/         # 9-phase build plan (Phase 0–8)
└── right-brain/         # Build tracking, logs, gaps, decisions
    ├── STATUS_Dashboard  # Module completion per domain
    ├── ACTIVATION_GUIDE  # How to start a build session
    ├── builder-logs/     # Per-module build session logs
    ├── validation/       # Post-build checklists
    ├── gaps/             # Bugs and missing specs discovered during build
    └── evolution/        # Architectural decision records (ADRs)
```

## Right-Brain Graph Color

**All right-brain files use `color: "#F97316"` (bright orange) in frontmatter.**  
This makes them visually distinct from all left-brain domain nodes in the Obsidian graph.

When creating any right-brain file (builder log, gap, ADR, validation report), always include:
```yaml
color: "#F97316"
```

## FlowFlex Slash Commands

These commands MUST be used to keep the right brain in sync with the left brain.

### `/flowflex:sync`
**Use after every build session or when modifying a left-brain spec.**

Steps to execute:
1. Identify: module name, domain, panel, status (in-progress/complete), bugs found, decisions made
2. **Update left-brain spec frontmatter** — set `status:`, `last_updated:`, add `right_brain_log: "[[builder-log-{module}]]"`
3. **Create or update builder log** at `right-brain/builder-logs/{module-name}.md`:
   - Frontmatter MUST include `color: "#F97316"`, `left_brain_source: "[[{module-slug}]]"`, `type: builder-log`
   - Session entry: what was built (file paths), decisions, problems, patterns found
   - Gaps section: link to any gap files created
4. **Update STATUS_Dashboard.md** — update Built count, emoji, recalculate %, add row to Recent Sessions
5. **Create gap files** for any bugs/spec issues found — `right-brain/gaps/gap_{slug}.md` with `color: "#F97316"` + update `MOC_Gaps.md`
6. **Create ADR files** for architectural decisions — `right-brain/evolution/decision-YYYY-MM-DD-{slug}.md` with `color: "#F97316"` + update `MOC_Evolution.md`

### `/flowflex:done [module=name]`
**Use when a module is fully built and tested.**

Steps to execute:
1. Run all sync steps above with `status: complete`
2. Run the build checklist from `right-brain/validation/build-checklist.md` — confirm every item
3. Update STATUS_Dashboard Built count for the domain (+1)
4. Update left-brain spec `status: complete`
5. If a validation report was created, add it to `right-brain/validation/MOC_Validation.md`

### `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]`
**Use when you find a bug or spec gap.**

Steps to execute:
1. Create `right-brain/gaps/gap_{slug}.md` with frontmatter:
   ```yaml
   ---
   type: gap
   severity: high | medium | low
   category: spec | architecture | feature | bug | data-model
   status: open
   color: "#F97316"
   discovered: YYYY-MM-DD
   discovered_in: {module-name}
   last_updated: YYYY-MM-DD
   ---
   ```
2. Document: context, the problem, impact, proposed solution, links
3. Add row to `right-brain/gaps/MOC_Gaps.md` Open Gaps table
4. If currently in a build session, link the gap from the active builder log under `## Gaps Discovered`

### `/flowflex:decision ["title"] [status=decided|proposed]`
**Use when an architectural decision is made.**

Steps to execute:
1. Create `right-brain/evolution/decision-{YYYY-MM-DD}-{slug}.md` with frontmatter:
   ```yaml
   ---
   type: adr
   date: YYYY-MM-DD
   status: decided | proposed
   color: "#F97316"
   ---
   ```
2. Document: context, options considered, decision, consequences, related left-brain files
3. Update related left-brain specs to reflect the decision
4. Add row to `right-brain/evolution/MOC_Evolution.md` Decision Log table

### `/flowflex:status [domain=name] [full]`
**Use to check current build state.**

Steps to execute:
1. Read `right-brain/STATUS_Dashboard.md` — show domain progress table
2. If `domain=name` specified: show that domain's module list with status
3. If `full`: also show open gaps (`right-brain/gaps/MOC_Gaps.md`) and recent decisions (`right-brain/evolution/MOC_Evolution.md`)

## Auto-Trigger Rules

The PostToolUse hook fires a reminder after every left-brain spec edit. When you see:
```
[FlowFlex Vault] Left-brain spec modified: {module} ({domain})
```
→ Run `/flowflex:sync` at the end of the task.

## Key Conventions

### Left-Brain
- Left-brain spec `status:` values: `planned` | `in-progress` | `complete` | `research`
- Every left-brain module note MUST have `right_brain_log:` frontmatter once build starts
- Every module note links to its parent MOC
- Gap files: `right-brain/gaps/gap_{slug}.md`
- ADR files: `right-brain/evolution/decision-YYYY-MM-DD-{slug}.md`

### Right-Brain
- ALL right-brain files: `color: "#F97316"` in frontmatter — makes them orange in Obsidian graph
- Builder logs: `right-brain/builder-logs/{module-name}.md`
- `left_brain_source:` frontmatter on every builder log → links right-brain back to left-brain spec

## Left-Brain Module Frontmatter Convention

```yaml
---
type: module
domain: HR & People
panel: hr
phase: 2
status: in-progress          # ← update this: planned | in-progress | complete
migration_range: 100000–109999
last_updated: YYYY-MM-DD     # ← update this
right_brain_log: "[[builder-log-leave-management]]"  # ← add when build starts
---
```

## Right-Brain Builder Log Frontmatter Convention

```yaml
---
type: builder-log
module: leave-management
domain: HR & People
panel: hr
phase: 2
started: YYYY-MM-DD
status: in-progress          # ← in-progress | complete
color: "#F97316"             # ← REQUIRED on all right-brain files
left_brain_source: "[[leave-management]]"   # ← links back to left-brain spec
last_updated: YYYY-MM-DD
---
```

## Tech Stack

- PHP 8.4 + Laravel 13
- Filament 5 (two panels: `/admin` for FlowFlex staff, `/app` for tenant users)
- PostgreSQL 17 + Redis 8
- Vue 3 + Inertia.js (public frontend)
- Livewire v4 (Filament components)
- Horizon + Redis (queues)
- ULID primary keys everywhere
- Global scopes for multi-tenancy (`company_id` on all tables)
- spatie/laravel-data (DTOs — `app/Data/{Domain}/`)
- spatie/laravel-permission (RBAC with teams = company_id)
- spatie/laravel-activitylog (audit trail)

## Key App Directory Structure

```
app/
├── Contracts/{Domain}/     # Service interfaces (e.g. EmployeeServiceInterface.php)
├── Services/{Domain}/      # Concrete implementations
├── Providers/{Domain}/     # ServiceProviders binding Interface → Service
├── Http/Controllers/       # Thin Inertia controllers (<10 lines each)
├── Data/{Domain}/          # spatie/laravel-data DTOs (input + output)
├── Models/                 # Eloquent models (HasUlids, BelongsToCompany, SoftDeletes)
├── Events/                 # Domain events (always carry company_id)
├── Filament/
│   ├── Admin/              # /admin panel resources (FlowFlex staff)
│   └── App/                # /app panel resources (tenant users)
└── Support/
    ├── Traits/BelongsToCompany.php
    ├── Traits/HasUlid.php
    ├── Scopes/CompanyScope.php
    └── Services/CompanyContext.php
```
