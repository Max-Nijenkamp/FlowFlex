---
domain: hr
module: recruitment
feature: job-requisitions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Job Requisitions

Not built — see [[../_module]].

## Purpose

Open role requests approved by HR, linked to a department and headcount plan. Optionally publish to the public careers page.

## Intended behavior

- Create requisition via `openRequisition(CreateRequisitionData)` — title, description, department, employment type, headcount.
- Status flow: `draft` → `open` → `closed`. Auto-closes when headcount is filled during hire.
- A publish toggle exposes the requisition on the careers page (`/careers/{slug}`); `slug` is sluggable, unique per company.
- Soft-depends on hr.workforce-planning to feed planned roles; without it, requisitions are created manually.

## Tables / permissions

- Table: `hr_job_requisitions`.
- Permissions: `hr.recruitment.create`, `hr.recruitment.update`, `hr.recruitment.view-any`.
- Filament: `JobRequisitionResource` (CRUD, publish toggle).

## UI

- **Kind**: simple-resource (`JobRequisitionResource`)
- **Page**: "Job Requisitions" (`/hr/job-requisitions`)
- **Layout**: table — title, department, employment type, headcount, status badge (draft/open/closed), publish toggle; create/edit form with description and a publish-to-careers toggle (exposes `/careers/{slug}`).
- **Key interactions**: `openRequisition` (create); edit; flip status draft → open → closed; toggle careers-page publish.
- **States**: empty ("No requisitions — open your first role") · loading (table skeleton) · error (inline banner) · selected (row opens requisition detail with applicants link).
- **Gating**: view requires `hr.recruitment.view-any`; create requires `hr.recruitment.create`; edit/publish requires `hr.recruitment.update`.

## Data

- Owns / writes: `hr_job_requisitions`
- Reads: reads department reference via `hr.profiles`/org data (department_id); soft-reads planned roles from `hr.workforce-planning` when present
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none *(soft: reads planned roles fed from [[../../workforce-planning/_module|hr.workforce-planning]] when present; manual requisitions without it)*
- Feeds: none directly *(auto-closes on hire via [[applicant-to-employee-conversion]] within this module)*
- Shared entity: department reference data (org structure); planned roles (workforce-planning)

## Test Checklist

### Unit
- [ ] `slug` is unique per company (sluggable)
- [ ] Requisition auto-closes when headcount is filled

### Feature (Pest)
- [ ] `openRequisition` creates a `draft`; status advances `draft → open → closed`
- [ ] Company A cannot see or edit company B requisitions

### Livewire
- [ ] Create denied without `hr.recruitment.create`; edit/publish denied without `hr.recruitment.update`
- [ ] Publish toggle exposes the requisition at `/careers/{slug}`

## Related

- [[../_module]] · [[../data-model]] · [[../architecture]]
