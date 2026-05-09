# FlowFlex — Claude Instructions

## Project

FlowFlex is an all-in-one SaaS platform. This repo contains:
- `flowflex-vault/` — Obsidian knowledge vault (specs, architecture, domain docs)
- Future: `app/` — Laravel + Filament PHP application

## Vault Structure

```
flowflex-vault/
├── left-brain/          # Specs, architecture, domain definitions (source of truth)
│   ├── architecture/    # System design docs
│   ├── domains/         # 31 business domains, each a Filament panel
│   ├── entities/        # Core data model entities
│   ├── concepts/        # Cross-cutting patterns
│   ├── design-system/   # Brand, colours, components
│   ├── frontend/        # Public Vue+Inertia pages
│   └── roadmap/         # 8-phase build plan
└── right-brain/         # Build tracking, logs, gaps, decisions
    ├── STATUS_Dashboard  # Module completion per domain
    ├── ACTIVATION_GUIDE  # How to start a build session
    ├── builder-logs/     # Per-module build session logs
    ├── validation/       # Post-build checklists
    ├── gaps/             # Bugs and missing specs discovered during build
    └── evolution/        # Architectural decision records (ADRs)
```

## FlowFlex Slash Commands

These commands MUST be used to keep the right brain in sync with the left brain.

### `/flowflex:sync`
**Use after every build session or when modifying a left-brain spec.**
- Updates `STATUS_Dashboard.md`
- Creates/updates builder log in `right-brain/builder-logs/`
- Adds `right_brain_log:` frontmatter link to the left-brain spec
- Updates left-brain `status:` (planned → in-progress → complete)
- Creates gap files for any bugs found
- Creates ADR files for architectural decisions

### `/flowflex:done [module=name]`
**Use when a module is fully built and tested.**
Fast-path to mark complete: runs sync, runs post-build checklist, updates STATUS_Dashboard count.

### `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]`
**Use when you find a bug or spec gap.**
Creates `right-brain/gaps/gap_{slug}.md` and updates `MOC_Gaps.md`.

### `/flowflex:decision ["title"] [status=decided|proposed]`
**Use when an architectural decision is made.**
Creates `right-brain/evolution/decision-{date}-{slug}.md` and updates `MOC_Evolution.md`.

### `/flowflex:status [domain=name] [full]`
**Use to check current build state.**
Reads STATUS_Dashboard, open gaps, recent decisions.

## Auto-Trigger Rules

The PostToolUse hook fires a reminder after every left-brain spec edit. When you see:
```
[FlowFlex Vault] Left-brain spec modified: {module} ({domain})
```
→ Run `/flowflex:sync` at the end of the task.

## Key Conventions

- Left-brain spec `status:` values: `planned` | `in-progress` | `complete` | `research`
- Every left-brain module note MUST have `right_brain_log:` frontmatter once build starts
- Every module note links to its parent MOC
- Builder logs go in `right-brain/builder-logs/{module-name}.md`
- Gap files: `right-brain/gaps/gap_{slug}.md`
- ADR files: `right-brain/evolution/decision-YYYY-MM-DD-{slug}.md`

## Frontmatter Convention (module notes)

```yaml
---
type: module
domain: HR & People
panel: hr
phase: 2
status: in-progress          # ← update this
migration_range: 100000–109999
last_updated: YYYY-MM-DD     # ← update this
right_brain_log: "[[builder-log-leave-management]]"  # ← add this when build starts
---
```

## Tech Stack (when app/ is built)

- PHP 8.4 + Laravel 12
- Filament 5 (admin panels — one per domain)
- PostgreSQL + read replica
- Vue 3 + Inertia.js (public frontend)
- Livewire (Filament components)
- Horizon + Redis (queues)
- ULID primary keys everywhere
- Global scopes for multi-tenancy (`company_id` on all tables)
