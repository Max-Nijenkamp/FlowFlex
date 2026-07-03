---
domain: hr
module: recruitment
feature: interview-scheduling
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Interview Scheduling

Not built — see [[../_module]].

## Purpose

Schedule interviews for applicants: date/time, interviewers, and interview type; record the outcome.

## Intended behavior

- Create/edit interviews via `InterviewResource` (CRUD): `scheduled_at`, `interviewers` (jsonb user ids), `type` (video/phone/on-site).
- Outcome tracked as `pass` / `fail` / `pending`; optional `notes`.
- Interviewer notification mails queued via notifications ([[../../../../infrastructure/mail]]).

## Tables / permissions

- Table: `hr_interviews`.
- Permissions: `hr.recruitment.update`, `hr.recruitment.view`, `hr.recruitment.view-any`.

## UI

- **Kind**: simple-resource (`InterviewResource`) *(a calendar/scheduler custom page is a possible richer variant)*
- **Page**: "Interviews" (`/hr/interviews`)
- **Layout**: table — applicant, scheduled-at, type (video/phone/on-site), interviewers, outcome badge (pass/fail/pending); create/edit form with a date-time picker, interviewer multi-select (user ids → jsonb), and notes.
- **Key interactions**: schedule/edit an interview; assign interviewers; record outcome + notes; interviewer notification mails queued.
- **States**: empty ("No interviews scheduled") · loading (table skeleton) · error (inline banner) · selected (row opens interview detail with outcome/notes).
- **Gating**: view requires `hr.recruitment.view` / `hr.recruitment.view-any`; schedule/edit requires `hr.recruitment.update`.

## Data

- Owns / writes: `hr_interviews`
- Reads: reads `hr_applicants` within this module; reads `users` (interviewer ids) via the platform user directory
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none
- Shared entity: platform users (interviewers) read via the user directory

## Test Checklist

### Unit
- [ ] `outcome` constrained to `pass` / `fail` / `pending`; `type` to video / phone / on-site
- [ ] `interviewers` stored as a jsonb array of user ids

### Feature (Pest)
- [ ] Scheduling an interview queues the interviewer notification mail
- [ ] Company A cannot see or edit company B interviews

### Livewire
- [ ] Schedule/edit denied without `hr.recruitment.update`
- [ ] Interviewer multi-select persists selected user ids

## Related

- [[../_module]] · [[../data-model]] · [[../architecture]]
