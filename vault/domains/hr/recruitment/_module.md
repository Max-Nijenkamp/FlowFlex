---
domain: hr
module: recruitment
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Recruitment

Job requisitions, applicant tracking pipeline, interview scheduling, and offer management. Intended to replace Breezy HR and Greenhouse (SMB tier).

> **Rebuild blueprint.** HR domain code was stripped under [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested — everything is `planned`. This spec is the rebuild target.

Priority: `v1` · Panel: `hr` · Permission prefix: `hr.recruitment`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | hire converts applicant → employee |
| Hard | core.billing + core.rbac | gating + permissions |
| Hard | core.files | CV uploads |
| Hard | core.notifications | applicant/interviewer mails |
| Soft | hr.workforce-planning | planned roles feed requisitions; manual requisitions without it |

---

## Intended Behavior

- Job requisitions: open role requests approved by HR, linked to department and headcount plan.
- Job posting: publish to careers page (Inertia Vue, public); optional LinkedIn/Indeed export deferred to P2 *(assumed: link-out only in v1)*.
- Applicant pipeline: custom stages per requisition (applied → screen → interview → offer → hired), driven by a `spatie/laravel-model-states` machine.
- Interview scheduling: date/time, interviewers, interview type (video/phone/on-site).
- Offer letter generation: template-based, salary/start-date fields; encrypted salary.
- Convert applicant to employee on hire (calls `EmployeeService::hire` — `EmployeeHired` fires from hr.profiles, not from here).
- Public application form stores applicant + CV; GDPR: applicant data retained 12 months then purged *(assumed)*.

---

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | none | — | recruitment fires no domain events |
| Handoff | `EmployeeService::hire(...)` *(assumed sync call, not an event)* | [[../employee-profiles/_module\|hr.profiles]] | hire converts applicant → employee; `EmployeeHired` fires **from hr.profiles**, not here |
| Consumes | none | — | soft-reads planned roles from [[../workforce-planning/_module\|hr.workforce-planning]] (data read, not an event) |

**Data ownership:** owns `hr_job_requisitions`, `hr_applicants`, `hr_interviews`, `hr_offers` (encrypted `hr_offers.salary_raw`). Reads department/planned-role reference data and platform users (interviewers); creates employees via `EmployeeService`, never writing `hr_employees` directly — [[../../../security/data-ownership]].

## Notes in this folder

- [[architecture]] — services/actions + state machines + Mermaid diagrams
- [[data-model]] — 4 tables + ERD
- [[api]] — DTOs, service methods, applicant→employee handoff
- [[security]] — permissions, tenancy, encryption, PII/CV handling
- [[unknowns]] — assumptions + open questions

### Features
- [[features/job-requisitions]]
- [[features/applicant-pipeline-kanban]]
- [[features/interview-scheduling]]
- [[features/offers]]
- [[features/applicant-to-employee-conversion]]

---

## Build Manifest

```
database/migrations/xxxx_create_hr_job_requisitions_table.php
database/migrations/xxxx_create_hr_applicants_table.php
database/migrations/xxxx_create_hr_interviews_table.php
database/migrations/xxxx_create_hr_offers_table.php
app/Models/HR/{JobRequisition,Applicant,Interview,Offer}.php
app/States/HR/Applicant/{ApplicantState,Applied,Screening,InterviewStage,OfferStage,Hired,Rejected}.php
app/Data/HR/{CreateRequisitionData,ApplyData,CreateOfferData,ApplicantData,RequisitionData,OfferData}.php
app/Contracts/HR/RecruitmentServiceInterface.php
app/Services/HR/RecruitmentService.php
app/Http/Controllers/CareersController.php + resources/js/Pages/Careers/{Index,Show,Apply}.vue
app/Mail/HR/{OfferMail,ApplicationReceivedMail,RejectionMail}.php
app/Console/Commands/HR/PurgeStaleApplicantsCommand.php
app/Filament/HR/Resources/{JobRequisitionResource,ApplicantResource,InterviewResource,OfferResource}.php
app/Filament/HR/Pages/ApplicantPipelinePage.php
database/factories/HR/{JobRequisitionFactory,ApplicantFactory,InterviewFactory,OfferFactory}.php
tests/Feature/HR/{RecruitmentPipelineTest,PublicApplyTest,HireConversionTest}.php
```

---

## Related

- [[../employee-profiles/_module]] — hire handoff
- [[../workforce-planning/_module]] — planned roles feed requisitions
- [[../../../glossary]]
