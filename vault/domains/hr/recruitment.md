---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.recruitment
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac, core.files, core.notifications]
soft-depends: [hr.workforce]
fires-events: []
consumes-events: []
patterns: [states, service, custom-pages, email]
tables: [hr_job_requisitions, hr_applicants, hr_interviews, hr_offers]
permission-prefix: hr.recruitment
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Recruitment

Job requisitions, applicant tracking pipeline, interview scheduling, and offer management. Replaces Breezy HR and Greenhouse (SMB tier).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | hire converts applicant → employee |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Hard | [[domains/core/file-storage\|core.files]] | CV uploads |
| Hard | [[domains/core/notifications\|core.notifications]] | applicant/interviewer mails |
| Soft | [[domains/hr/workforce-planning\|hr.workforce]] | planned roles feed requisitions; manual requisitions without it |

---

## Core Features

- Job requisitions: open role requests approved by HR, linked to department and headcount plan
- Job posting: publish to careers page (Inertia Vue page — public), optional LinkedIn/Indeed export (P2 *(assumed: link-out only in v1)*)
- Applicant pipeline: custom stages per requisition (applied → screen → interview → offer → hired)
- Applicant status machine via `spatie/laravel-model-states`
- Interview scheduling: date/time, interviewers, interview type (video/phone/on-site)
- Offer letter generation: template-based, salary/start date fields
- Convert applicant to employee on hire (calls `EmployeeService::hire` — fires EmployeeHired from hr.profiles, not from here)
- Public application form stores applicant + CV; GDPR: applicant data retained 12 months then purged *(assumed)*

---

## Data Model

### hr_job_requisitions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), department_id FK nullable | ulid | |
| title | string | |
| description | text | published to careers page |
| employment_type | string | full-time/part-time/contractor |
| status | string default `draft` | draft / open / closed |
| slug | string | sluggable, unique per company — careers URL |
| open_date | date nullable | |
| headcount | int default 1 | |
| deleted_at | timestamp nullable | |

### hr_applicants

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), requisition_id FK | ulid | |
| first_name / last_name / email / phone | string (phone E.164) | |
| cv_path | string nullable | via core.files |
| status | string default `applied` | state machine |
| source | string nullable | careers / referral / manual |
| rejection_reason | text nullable | *(assumed)* |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, requisition_id, status)`

### hr_interviews

| Column | Type | Notes |
|---|---|---|
| id, company_id, applicant_id FK | ulid | |
| scheduled_at | timestamp | |
| interviewers | jsonb | user ids |
| type | string | video / phone / on-site |
| outcome | string nullable | pass / fail / pending |
| notes | text nullable | |

### hr_offers

| Column | Type | Notes |
|---|---|---|
| id, company_id, applicant_id FK | ulid | |
| salary_cents | bigint | |
| currency | string(3) | |
| start_date | date | |
| status | string default `draft` | draft / sent / accepted / declined |
| sent_at / accepted_at | timestamp nullable | |

---

## State Machine

Column: `hr_applicants.status` — `ApplicantState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `applied` | `screening` | `hr.recruitment.update` | |
| `screening` | `interview` | `hr.recruitment.update` | |
| `interview` | `offer` | `hr.recruitment.update` | offer record expected |
| `offer` | `hired` | `hr.recruitment.hire` | converts to employee via `EmployeeService::hire` |
| any non-terminal | `rejected` | `hr.recruitment.update` | rejection mail *(assumed: optional toggle)* |

Initial: `applied`. Terminal: `hired`, `rejected`. Audited.

---

## DTOs

### CreateRequisitionData — title (required, max:150), description (required), department_id (nullable ulid), employment_type (in set), headcount (min:1)
### ApplyData (public form) — first_name/last_name (required), email (required, email), phone (nullable, phone:AUTO), cv (file: pdf/docx, max 10MB), requisition slug; rate-limited + honeypot *(assumed)*
### CreateOfferData — applicant_id, salary_cents (min:0), currency, start_date (after:today)
### ApplicantData (output) — id, name, email, requisition_title, status, source, days_in_stage

## Services & Actions

Interface→Service: `RecruitmentServiceInterface` → `RecruitmentService`.

- `openRequisition(CreateRequisitionData $data): RequisitionData`
- `apply(ApplyData $data): ApplicantData` — public path, company resolved from requisition slug
- `moveStage(string $applicantId, string $state): ApplicantData`
- `makeOffer(CreateOfferData $data): OfferData` / `sendOffer(string $offerId)`
- `hire(string $applicantId): EmployeeData` — delegates to `EmployeeService::hire`, closes requisition when headcount filled

---

## Filament

**Nav group:** Employees

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `JobRequisitionResource` | #1 CRUD resource | publish toggle |
| `ApplicantPipelinePage` | #3 Kanban custom page | per-requisition columns by state, drag = moveStage; polling 30s (not collaborative enough for Reverb) |
| `ApplicantResource` | #1 CRUD resource | list view + CV preview |
| `InterviewResource` | #1 CRUD resource | schedule + outcome |
| `OfferResource` | #1 CRUD resource | create, send, track |

Public careers pages: Vue + Inertia (`/careers`, `/careers/{slug}`) — ui-strategy row #16.

---

## Permissions

`hr.recruitment.view-any` · `hr.recruitment.view` · `hr.recruitment.create` · `hr.recruitment.update` · `hr.recruitment.delete` · `hr.recruitment.hire` · `hr.recruitment.manage-offers`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `PurgeStaleApplicantsCommand` | default | weekly | rejected/withdrawn > 12 months, date guard |
| Offer/rejection mails | notifications | on action | — |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Public apply creates applicant under requisition's company without auth
- [ ] Apply route rate-limited; oversized/wrong-type CV rejected
- [ ] Hire converts applicant → employee (EmployeeHired fires from profiles service)
- [ ] Requisition auto-closes at headcount filled
- [ ] Invalid stage jumps rejected (applied → offer)
- [ ] Stale applicant purge respects 12-month guard

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

- [[domains/hr/employee-profiles]]
- [[domains/hr/workforce-planning]]
- [[frontend/_index]] — careers pages
