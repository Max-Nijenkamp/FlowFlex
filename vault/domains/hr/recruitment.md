---
type: module
domain: HR & People
panel: hr
module-key: hr.recruitment
status: planned
color: "#4ADE80"
---

# Recruitment

Job requisitions, applicant tracking pipeline, interview scheduling, and offer management. Replaces Breezy HR and Greenhouse (SMB tier).

---

## Core Features

- Job requisitions: open role requests approved by HR, linked to department and headcount plan
- Job posting: publish to careers page (Inertia Vue page), optional LinkedIn/Indeed export
- Applicant pipeline: custom stages per requisition (applied → screen → interview → offer → hired)
- Applicant status machine via `spatie/laravel-model-states`
- Interview scheduling: date/time, interviewers, interview type (video/phone/on-site)
- Offer letter generation: template-based, salary/start date fields
- Convert applicant to employee on hire (creates `hr_employees` record)

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_job_requisitions` | company_id, title, department_id, employment_type, status, open_date, headcount |
| `hr_applicants` | company_id, requisition_id, first_name, last_name, email, phone, cv_path, status, source |
| `hr_interviews` | company_id, applicant_id, scheduled_at, interviewers (json), type, outcome |
| `hr_offers` | company_id, applicant_id, salary, start_date, status, sent_at, accepted_at |

---

## Filament

- `JobRequisitionResource` — manage open roles
- `ApplicantResource` — per-requisition pipeline view (custom Kanban page) + list view
- `InterviewResource` — schedule and record interview outcomes
- `OfferResource` — create, send, track offer letters

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/workforce-planning]]
