---
domain: hr
module: recruitment
feature: applicant-pipeline-kanban
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Applicant Pipeline (Kanban)

Not built — see [[../_module]].

## Purpose

Track applicants through per-requisition stages: `applied → screening → interview → offer → hired`, with `rejected` reachable from any non-terminal state.

## Intended behavior

- Custom Filament page `ApplicantPipelinePage`: columns by state, per requisition.
- Drag a card = `moveStage($applicantId, $state)`; transitions guarded by `ApplicantState` (spatie/laravel-model-states). Invalid jumps (e.g. `applied → offer`) are rejected.
- Polling 30s (not collaborative enough for Reverb).
- `→ rejected` may send rejection mail *(assumed: optional toggle)* and records `rejection_reason` *(assumed)*.

## Tables / permissions

- Table: `hr_applicants` (state machine on `status`; index `(company_id, requisition_id, status)`).
- Permissions: `hr.recruitment.update` (moves), `hr.recruitment.hire` (offer → hired), `hr.recruitment.view-any`.
- Custom page must state `canAccess()` explicitly.

## UI

- **Kind**: custom-page (Kanban board)
- **Page**: "Applicant Pipeline" (`/hr/applicant-pipeline`)
- **Layout**: `ApplicantPipelinePage` Kanban — columns by state (`applied → screening → interview → offer → hired`, plus `rejected`), per requisition; applicant cards show name, source, requisition. 30s polling.
- **Key interactions**: drag a card between columns = `moveStage($applicantId, $state)`, guarded by `ApplicantState`; `→ rejected` optionally sends rejection mail *(assumed: toggle)* and records `rejection_reason` *(assumed)*.
- **States**: empty ("No applicants for this requisition") · loading (board skeleton) · error (toast on invalid transition, e.g. `applied → offer`) · selected (card opens applicant detail drawer).
- **Gating**: view requires `hr.recruitment.view-any`; moves require `hr.recruitment.update`; offer → hired requires `hr.recruitment.hire`. Custom page declares `canAccess()` explicitly.

## Data

- Owns / writes: `hr_applicants` (state machine on `status`; index `(company_id, requisition_id, status)`)
- Reads: reads `hr_job_requisitions` (columns per requisition) within this module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none directly *(hire handoff modeled in [[applicant-to-employee-conversion]])*
- Shared entity: none

## Test Checklist

### Unit
- [ ] Invalid stage jump (e.g. `applied → offer`) rejected by `ApplicantState`
- [ ] `→ rejected` reachable from any non-terminal state; records `rejection_reason` *(assumed)*

### Feature (Pest)
- [ ] `moveStage` advances only along legal transitions; concurrent moves on one applicant serialized by `lockForUpdate`
- [ ] `offer → hired` requires `hr.recruitment.hire`; company A cannot move company B applicants

### Livewire
- [ ] `ApplicantPipelinePage` `canAccess()` gated by permission + `hasModule('hr.recruitment')`
- [ ] Drag move requires `hr.recruitment.update`; invalid transition surfaces an error toast

## Related

- [[../_module]] · [[../architecture]] · [[../../../../architecture/patterns/custom-pages]] · [[../../../../architecture/patterns/states]]
