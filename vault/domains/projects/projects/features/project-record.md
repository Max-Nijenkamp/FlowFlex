---
domain: projects
module: projects
feature: project-record
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Project Record & Health

Create and manage a project: metadata, status machine, budget, and computed health.

## Behaviour

- CRUD a project; creator auto-added as an `owner` member.
- Status machine `planning → active → on_hold → completed | cancelled`; `completed` stamps `completed_at`.
- Health = completion % vs elapsed-timeline %: on-track / at-risk (>15pt behind) / off-track (>30pt) *(assumed)*.
- Budget: estimated hours/cost vs actuals (actuals read from time entries, 0 when time module off).

## UI

- **Kind**: simple-resource (list/form) + a #2 detail view page with tabs.
- **Page**: `ProjectResource` at `/app/projects/projects`; detail view `/app/projects/projects/{id}`.
- **Layout**: table (name, status badge, health chip, owner, target date, progress bar). Detail = tabs Overview / Tasks / Sprints / Milestones / Files / Time (soft-dep tabs shown only when those modules active).
- **Key interactions**: create/edit form; status transition actions gated by the machine; health chip colour-coded.
- **States**: empty (no projects → "Create your first project" CTA) · loading (table skeleton) · error (toast) · selected (row → detail).
- **Gating**: `projects.projects.view-any`/`view`; edits require `projects.projects.update`.

## Data

- Owns / writes: `proj_projects` (+ owner row in `proj_project_members`).
- Reads: `crm.contacts`/`crm.accounts` (client link) and `projects.time` (actuals) via their read APIs.
- Cross-domain writes: none — client link is a stored foreign id, never a `crm_*` write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (reads only).
- Feeds: nothing (no domain events in v1).
- Shared entity: `crm_accounts` (client), `users` (owner/members).

## Test Checklist

### Unit
- [ ] Health math returns on-track / at-risk (>15pt behind) / off-track (>30pt) at the boundary fixtures *(assumed)*.
- [ ] `CreateProjectData` rejects `target_date` before `start_date` ("Target date must be on or after the start date.").

### Feature (Pest)
- [ ] Create auto-adds the creator as an `owner` row in `proj_project_members`.
- [ ] `completed` transition stamps `completed_at`; illegal transitions rejected by the state machine.
- [ ] `ProjectService::actuals()` returns 0 (no error) when `projects.time` is inactive.
- [ ] Tenant isolation: company A cannot read/edit company B's project.

### Livewire
- [ ] `ProjectResource` denied without `projects.projects.view-any`/`view`; hidden when `projects.projects` inactive.
- [ ] Status transition actions gated by `projects.projects.update` and constrained to legal machine edges.

## Unknowns

- Confirm-modal on completing with open tasks *(assumed)*. Archive vs soft-delete open ([[../unknowns]]).

## Related

- [[../_module|Projects]] · [[project-membership|Membership]] · [[../../tasks/_module|Tasks]]
