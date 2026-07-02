---
domain: projects
module: templates
feature: instantiate-project
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Instantiate from Template

A wizard that materialises a full project (sections, tasks, milestones) from a template.

## Behaviour

- Pick a template → name + start date + owner/members → confirm.
- One transaction: create project + sections + tasks (due = start + `day_offset`) + milestones; delegate to owning-module actions.
- Atomic: any failure rolls the whole thing back.

## UI

- **Kind**: custom-page (wizard — [[../../../../architecture/patterns/feature-ui-spec]] custom-page kind).
- **Page**: `CreateProjectFromTemplatePage` at `/app/projects/templates/create` (nav group Settings).
- **Layout**: stepper — (1) choose template (cards, category filter) → (2) project name + start date + owner/members → (3) preview of generated sections/tasks/milestones with computed dates → confirm.
- **Key interactions**: step navigation; live due-date preview from start date; confirm → single-transaction instantiate → deep-link to the new project.
- **States**: empty (no templates → link to author one) · loading (instantiating) · error (roll-back → "Couldn't create project, nothing was saved") · selected (template card highlighted) · success (redirect to project).
- **Gating**: `projects.templates.instantiate` (+ implicitly needs projects/tasks/milestones create rights).

## Data

- Owns / writes: nothing of its own beyond reading template rows.
- Reads: `proj_template_*` (own).
- Cross-domain writes: none — creates the project/tasks/milestones through **projects.projects / tasks / milestones actions**, never a direct write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: creates real records via projects.projects / projects.tasks / projects.milestones actions.
- Shared entity: `proj_projects`, `proj_tasks`, `proj_milestones` (owned by their modules).

## Unknowns

- Weekend-skipping date math; carrying dependencies — see [[../unknowns]].

## Related

- [[../_module|Templates]] · [[template-authoring|Template Authoring]] · [[../../projects/_module|Projects]]
