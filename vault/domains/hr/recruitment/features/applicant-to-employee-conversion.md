---
domain: hr
module: recruitment
feature: applicant-to-employee-conversion
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Applicant → Employee Conversion (Hire)

Not built — see [[../_module]].

## Purpose

Convert a hired applicant into an employee record, delegating to hr.profiles.

## Intended behavior

- `hire($applicantId)` requires the applicant in `offer` state and permission `hr.recruitment.hire`.
- Delegates to `EmployeeService::hire(...)` in [[../../employee-profiles/_module]]; `EmployeeHired` fires **from hr.profiles**, not from recruitment (this module fires no events).
- On success, applicant transitions to `hired`; the requisition auto-closes if headcount is filled.

## Tables / permissions

- Tables: `hr_applicants` (status → `hired`), `hr_job_requisitions` (status → `closed` when filled).
- Permission: `hr.recruitment.hire`.
- Hard dependency: hr.profiles must be complete for this path to work.

## UI

- **Kind**: custom-page action (Hire action on the pipeline / applicant detail; the delegated employee creation runs background)
- **Page**: "Applicant Pipeline" (`/hr/applicant-pipeline`) — **Hire** action on an applicant in `offer` state
- **Layout**: no standalone screen; a **Hire** action (confirmation modal) on the applicant card/detail; on success the card moves to `hired` and the requisition auto-closes if headcount is filled.
- **Key interactions**: click **Hire** → `hire($applicantId)`; confirm; system delegates to `EmployeeService::hire(...)`.
- **States**: empty (n/a — action only shown in `offer` state) · loading (action spinner) · error (toast if applicant not in `offer` state or `hr.profiles` unavailable) · selected (confirmation modal before commit).
- **Gating**: requires `hr.recruitment.hire`; applicant must be in `offer` state.

## Data

- Owns / writes: `hr_applicants` (status → `hired`), `hr_job_requisitions` (status → `closed` when headcount filled)
- Reads: reads `hr_offers`/applicant context within this module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]); employee record created through `EmployeeService::hire` in hr.profiles, not by writing `hr_employees` directly

## Relations

- Consumes: none
- Feeds: hands off to [[../../employee-profiles/_module|hr.profiles]] via `EmployeeService::hire(...)`; `EmployeeHired` fires **from hr.profiles** on record creation, not from recruitment. Exact handoff mechanism (direct service call vs. queued `ApplicantHired` event bridge) is *(assumed)* a synchronous service call.
- Shared entity: `hr_employees` (created via EmployeeService); requisition headcount (this module)

## Test Checklist

### Unit
- [ ] `hire` refuses an applicant not in `offer` state
- [ ] Requisition auto-closes only when headcount is filled

### Feature (Pest)
- [ ] `hire` delegates to `EmployeeService::hire`, transitions applicant → `hired`, and closes the filled requisition
- [ ] `EmployeeHired` fires from hr.profiles, not from recruitment (this module fires no events); concurrent hire serialized by `lockForUpdate`

### Livewire
- [ ] Hire action requires `hr.recruitment.hire` and is shown only for an applicant in `offer` state

## Related

- [[../_module]] · [[../api]] · [[../../employee-profiles/_module]]
