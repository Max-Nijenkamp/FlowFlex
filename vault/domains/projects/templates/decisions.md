---
domain: projects
module: templates
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Templates — Decisions

## ADR: Instantiation delegates to owning-module actions

- **Context:** Creating a project from a template materialises projects, tasks, and milestones.
- **Decision:** `TemplateService::instantiate` calls projects.projects / tasks / milestones actions inside one transaction — it never writes `proj_projects`/`proj_tasks`/`proj_milestones` directly.
- **Consequences:** Honours data-ownership; template rows are the only tables templates writes; validation/state-machines of the owning modules apply.

## ADR: System templates are read-only with copy-on-edit *(assumed)*

- **Decision:** Seeded system templates (`company_id` null, `is_system`) are visible to all tenants read-only; editing copies into the company.
- **Consequences:** Shared starter content without cross-tenant mutation risk; a documented global-scope exception.

## ADR: Day-offset scheduling, calendar days v1 *(assumed)*

- **Decision:** Template tasks/milestones use `day_offset` from project start; v1 uses calendar days (weekend-skipping later).
- **Consequences:** Simple due-date math; noted as assumed ([[unknowns]]).

## ADR: Atomic instantiation

- **Decision:** The whole instantiation runs in one DB transaction; any failure rolls all back.
- **Consequences:** No half-created projects.
