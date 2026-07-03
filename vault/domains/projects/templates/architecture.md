---
domain: projects
module: templates
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Templates — Architecture

## Services & Actions

- `TemplateService::instantiate(CreateFromTemplateData): ProjectData` — single transaction: create project + sections + tasks (due = start + `day_offset`) + milestones; **delegates creation to the owning modules' actions** (never a direct cross-table write).
- `TemplateService::fromProject(SaveAsTemplateData): TemplateData` — reverse: computes offsets from the project start.

## System templates

Seeded rows with `company_id` null + `is_system = true`; readable by all companies via a **read-only global-scope exception**; never editable cross-tenant (edit → copy into the company). See [[../../../architecture/multi-tenancy]].

## Events

None cross-domain. Instantiation is synchronous via owning-module actions.

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ProjectTemplateResource` | #1 CRUD resource | tweaks: inline-relation-repeater (section/task/milestone repeaters), custom-header-actions (duplicate-to-edit, save-as-template) | system templates render read-only per-row with a "Duplicate to edit" action; list filters: category, system/company *(assumed)* |
| `CreateProjectFromTemplatePage` | #7 Wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] | stepper: choose template → name/start date/owner/members → date preview → confirm; single-transaction instantiate |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.templates.view-any') && BillingService::hasModule('projects.templates')`
per [[../../../architecture/filament-patterns]] #1. `CreateProjectFromTemplatePage` is a custom page and MUST state
this explicitly — Filament does not auto-gate custom pages. The wizard's instantiate step additionally requires
`projects.templates.instantiate` (plus the target modules' create rights, enforced by their owning actions).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Template CRUD (form, repeaters, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Instantiate project from template (`TemplateService::instantiate`) | Pessimistic (transactional) | single `DB::transaction()` creating project + sections + tasks + milestones via the owning-module actions — append-only but must be all-or-nothing, so any failure rolls the whole set back; no shared counter, hence no row locks per [[../../../architecture/patterns/states]] |
| Save project as template (`TemplateService::fromProject`) | Pessimistic (transactional) | single `DB::transaction()` writing the template + section/task/milestone rows atomically |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

None.
